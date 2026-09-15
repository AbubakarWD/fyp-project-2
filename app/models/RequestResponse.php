<?php
/**
 * BloodLife — RequestResponse Model
 * Manages donor responses to emergency blood requests and status updates.
 */

require_once __DIR__ . '/../config/database.php';

class RequestResponse {

    /**
     * Submit a donor response to a blood request. Prevent duplicates via DB unique key.
     *
     * @param int $requestId
     * @param int $donorId
     * @param int $unitsOffered
     * @param string $donorNote
     * @return int Response ID
     * @throws Exception
     */
    public static function create(int $requestId, int $donorId, int $unitsOffered = 1, string $donorNote = ''): int {
        $pdo = Database::getInstance();

        // Check duplicate response
        $existing = self::findByRequestAndDonor($requestId, $donorId);
        if ($existing) {
            throw new Exception("You have already submitted a response to this blood request.");
        }

        $stmt = $pdo->prepare("
            INSERT INTO request_responses (request_id, donor_id, units_offered, status, donor_note)
            VALUES (:request_id, :donor_id, :units_offered, 'pending', :donor_note)
        ");

        $stmt->execute([
            'request_id'    => $requestId,
            'donor_id'      => $donorId,
            'units_offered' => $unitsOffered,
            'donor_note'    => trim($donorNote)
        ]);

        return (int)$pdo->lastInsertId();
    }

    /**
     * Find existing response by request ID and donor ID.
     *
     * @param int $requestId
     * @param int $donorId
     * @return array|null
     */
    public static function findByRequestAndDonor(int $requestId, int $donorId): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT * FROM request_responses 
            WHERE request_id = :request_id AND donor_id = :donor_id 
            LIMIT 1
        ");
        $stmt->execute(['request_id' => $requestId, 'donor_id' => $donorId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find response by response ID with detailed donor & request join.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT rr.*, p.full_name as donor_name, p.phone_number as donor_phone, p.city as donor_city,
                   bg.code as donor_blood_group, r.requester_id, r.patient_name
            FROM request_responses rr
            JOIN user_profiles p ON rr.donor_id = p.user_id
            JOIN blood_groups bg ON p.blood_group_id = bg.id
            JOIN blood_requests r ON rr.request_id = r.id
            WHERE rr.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get all donor responses submitted for a specific request.
     *
     * @param int $requestId
     * @return array
     */
    public static function getResponsesByRequestId(int $requestId): array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT rr.*, p.full_name as donor_name, p.phone_number as donor_phone, p.city as donor_city,
                   p.avatar as donor_avatar, bg.code as donor_blood_group
            FROM request_responses rr
            JOIN user_profiles p ON rr.donor_id = p.user_id
            JOIN blood_groups bg ON p.blood_group_id = bg.id
            WHERE rr.request_id = :request_id
            ORDER BY rr.created_at DESC
        ");
        $stmt->execute(['request_id' => $requestId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all responses submitted by a specific donor.
     *
     * @param int $donorId
     * @return array
     */
    public static function getResponsesByDonorId(int $donorId): array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT rr.*, r.patient_name, r.hospital_name, r.city, r.urgency_level,
                   bg.code as blood_group, p.full_name as requester_name
            FROM request_responses rr
            JOIN blood_requests r ON rr.request_id = r.id
            JOIN blood_groups bg ON r.blood_group_id = bg.id
            JOIN user_profiles p ON r.requester_id = p.user_id
            WHERE rr.donor_id = :donor_id
            ORDER BY rr.created_at DESC
        ");
        $stmt->execute(['donor_id' => $donorId]);
        return $stmt->fetchAll();
    }

    /**
     * Update status of response ('pending', 'accepted', 'rejected', 'completed', 'cancelled').
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public static function updateStatus(int $id, string $status): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE request_responses SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }
}
