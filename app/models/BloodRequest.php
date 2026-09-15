<?php
/**
 * BloodLife — BloodRequest Model
 * Data access and lifecycle management for emergency blood requests.
 */

require_once __DIR__ . '/../config/database.php';

class BloodRequest {

    /**
     * Create a new emergency blood request.
     *
     * @param array $data Contains requester_id, patient_name, blood_group_id, units_required, urgency_level, hospital_name, hospital_address, city, contact_number, required_date, medical_reason
     * @return int Created Request ID
     */
    public static function create(array $data): int {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            INSERT INTO blood_requests 
            (requester_id, patient_name, blood_group_id, units_required, units_fulfilled, urgency_level, hospital_name, hospital_address, city, contact_number, required_date, medical_reason, status)
            VALUES 
            (:requester_id, :patient_name, :blood_group_id, :units_required, 0, :urgency_level, :hospital_name, :hospital_address, :city, :contact_number, :required_date, :medical_reason, 'active')
        ");

        $stmt->execute([
            'requester_id'     => (int)$data['requester_id'],
            'patient_name'     => trim($data['patient_name']),
            'blood_group_id'   => (int)$data['blood_group_id'],
            'units_required'   => (int)($data['units_required'] ?? 1),
            'urgency_level'    => $data['urgency_level'] ?? 'medium',
            'hospital_name'    => trim($data['hospital_name']),
            'hospital_address' => trim($data['hospital_address'] ?? ''),
            'city'             => trim($data['city']),
            'contact_number'   => trim($data['contact_number']),
            'required_date'    => $data['required_date'],
            'medical_reason'   => trim($data['medical_reason'] ?? '')
        ]);

        return (int)$pdo->lastInsertId();
    }

    /**
     * Find blood request by ID with requester and blood group details.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT r.*, bg.code as blood_group, bg.display_name as blood_group_name,
                   u.email as requester_email, p.full_name as requester_name, p.phone_number as requester_phone
            FROM blood_requests r
            JOIN blood_groups bg ON r.blood_group_id = bg.id
            JOIN users u ON r.requester_id = u.id
            JOIN user_profiles p ON u.id = p.user_id
            WHERE r.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get blood requests with dynamic filtering, pagination, and sorting.
     *
     * @param array $filters Allows 'blood_group_id', 'city', 'urgency_level', 'status', 'requester_id'
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getAll(array $filters = [], int $limit = 20, int $offset = 0): array {
        $pdo = Database::getInstance();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['blood_group_id'])) {
            $where[] = "r.blood_group_id = :blood_group_id";
            $params['blood_group_id'] = (int)$filters['blood_group_id'];
        }

        if (!empty($filters['city'])) {
            $where[] = "r.city = :city";
            $params['city'] = trim($filters['city']);
        }

        if (!empty($filters['urgency_level'])) {
            $where[] = "r.urgency_level = :urgency_level";
            $params['urgency_level'] = trim($filters['urgency_level']);
        }

        if (!empty($filters['status'])) {
            $where[] = "r.status = :status";
            $params['status'] = trim($filters['status']);
        }

        if (!empty($filters['requester_id'])) {
            $where[] = "r.requester_id = :requester_id";
            $params['requester_id'] = (int)$filters['requester_id'];
        }

        $whereClause = implode(" AND ", $where);

        $sql = "
            SELECT r.*, bg.code as blood_group, bg.display_name as blood_group_name,
                   p.full_name as requester_name, p.avatar as requester_avatar,
                   (SELECT COUNT(*) FROM request_responses rr WHERE rr.request_id = r.id) as response_count
            FROM blood_requests r
            JOIN blood_groups bg ON r.blood_group_id = bg.id
            JOIN user_profiles p ON r.requester_id = p.user_id
            WHERE {$whereClause}
            ORDER BY 
                FIELD(r.urgency_level, 'critical', 'high', 'medium', 'low'),
                r.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count total blood requests matching filters.
     *
     * @param array $filters
     * @return int
     */
    public static function countAll(array $filters = []): int {
        $pdo = Database::getInstance();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['blood_group_id'])) {
            $where[] = "blood_group_id = :blood_group_id";
            $params['blood_group_id'] = (int)$filters['blood_group_id'];
        }

        if (!empty($filters['city'])) {
            $where[] = "city = :city";
            $params['city'] = trim($filters['city']);
        }

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = trim($filters['status']);
        }

        if (!empty($filters['requester_id'])) {
            $where[] = "requester_id = :requester_id";
            $params['requester_id'] = (int)$filters['requester_id'];
        }

        $whereClause = implode(" AND ", $where);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blood_requests WHERE {$whereClause}");
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    /**
     * Update status of blood request ('active', 'partially_fulfilled', 'fulfilled', 'cancelled', 'expired').
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public static function updateStatus(int $id, string $status): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE blood_requests SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Increment or update units fulfilled and update status conditionally.
     *
     * @param int $id
     * @param int $unitsFulfilled
     * @return bool
     */
    public static function updateFulfilledUnits(int $id, int $unitsFulfilled): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            UPDATE blood_requests 
            SET units_fulfilled = :units_fulfilled,
                status = CASE 
                    WHEN :val_check1 >= units_required THEN 'fulfilled'
                    WHEN :val_check2 > 0 THEN 'partially_fulfilled'
                    ELSE status 
                END
            WHERE id = :id
        ");
        return $stmt->execute([
            'units_fulfilled' => $unitsFulfilled,
            'val_check1'      => $unitsFulfilled,
            'val_check2'      => $unitsFulfilled,
            'id'              => $id
        ]);
    }
}
