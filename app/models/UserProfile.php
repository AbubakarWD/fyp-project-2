<?php
/**
 * BloodLife — UserProfile Model
 * Handles user profile details, donor availability, and donor search filtering.
 */

require_once __DIR__ . '/../config/database.php';

class UserProfile {

    /**
     * Find profile by user ID with blood group information.
     *
     * @param int $userId
     * @return array|null
     */
    public static function findByUserId(int $userId): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT p.*, u.email, u.username, u.primary_role, u.status as account_status,
                   bg.code as blood_group, bg.display_name as blood_group_name
            FROM user_profiles p
            JOIN users u ON p.user_id = u.id
            JOIN blood_groups bg ON p.blood_group_id = bg.id
            WHERE p.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find profile by user email with blood group information.
     *
     * @param string $email
     * @return array|null
     */
    public static function findByEmail(string $email): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT p.*, u.email, u.username, u.primary_role, u.status as account_status,
                   bg.code as blood_group, bg.display_name as blood_group_name
            FROM user_profiles p
            JOIN users u ON p.user_id = u.id
            JOIN blood_groups bg ON p.blood_group_id = bg.id
            WHERE u.email = :email
            LIMIT 1
        ");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Update user profile attributes.
     *
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public static function update(int $userId, array $data): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            UPDATE user_profiles
            SET full_name = :full_name,
                phone_number = :phone_number,
                gender = :gender,
                blood_group_id = :blood_group_id,
                city = :city,
                address = :address,
                bio = :bio,
                is_available_donor = :is_available_donor
            WHERE user_id = :user_id
        ");

        return $stmt->execute([
            'full_name'          => trim($data['full_name']),
            'phone_number'       => trim($data['phone_number']),
            'gender'             => $data['gender'] ?? 'other',
            'blood_group_id'     => (int)$data['blood_group_id'],
            'city'               => trim($data['city']),
            'address'            => trim($data['address'] ?? ''),
            'bio'                => trim($data['bio'] ?? ''),
            'is_available_donor' => !empty($data['is_available_donor']) ? 1 : 0,
            'user_id'            => $userId
        ]);
    }

    /**
     * Update user avatar filename.
     *
     * @param int $userId
     * @param string $avatarFilename
     * @return bool
     */
    public static function updateAvatar(int $userId, string $avatarFilename): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE user_profiles SET avatar = :avatar WHERE user_id = :user_id");
        return $stmt->execute(['avatar' => $avatarFilename, 'user_id' => $userId]);
    }

    /**
     * Toggle donor availability status.
     *
     * @param int $userId
     * @param bool $isAvailable
     * @return bool
     */
    public static function toggleAvailability(int $userId, bool $isAvailable): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE user_profiles SET is_available_donor = :is_available WHERE user_id = :user_id");
        return $stmt->execute(['is_available' => $isAvailable ? 1 : 0, 'user_id' => $userId]);
    }

    /**
     * Search available blood donors with filtering by blood group, city, availability, and name.
     *
     * @param array $filters Allows 'blood_group_id', 'city', 'is_available_donor', 'search'
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function searchDonors(array $filters = [], int $limit = 20, int $offset = 0): array {
        $pdo = Database::getInstance();
        $where = ["u.status = 'active'", "(u.primary_role = 'donor' OR u.primary_role = 'both')"];
        $params = [];

        if (!empty($filters['blood_group_id'])) {
            $where[] = "p.blood_group_id = :blood_group_id";
            $params['blood_group_id'] = (int)$filters['blood_group_id'];
        }

        if (!empty($filters['city'])) {
            $where[] = "p.city = :city";
            $params['city'] = trim($filters['city']);
        }

        if (isset($filters['is_available_donor']) && $filters['is_available_donor'] !== '') {
            $where[] = "p.is_available_donor = :is_available_donor";
            $params['is_available_donor'] = (int)$filters['is_available_donor'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(p.full_name LIKE :search_name OR p.city LIKE :search_city)";
            $params['search_name'] = '%' . trim($filters['search']) . '%';
            $params['search_city'] = '%' . trim($filters['search']) . '%';
        }

        $whereClause = implode(" AND ", $where);

        $sql = "
            SELECT p.*, u.email, u.username, bg.code as blood_group, bg.display_name as blood_group_name
            FROM user_profiles p
            JOIN users u ON p.user_id = u.id
            JOIN blood_groups bg ON p.blood_group_id = bg.id
            WHERE {$whereClause}
            ORDER BY p.is_available_donor DESC, p.total_donations_count DESC, p.full_name ASC
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
     * Count total donors matching filters.
     *
     * @param array $filters
     * @return int
     */
    public static function countDonors(array $filters = []): int {
        $pdo = Database::getInstance();
        $where = ["u.status = 'active'", "(u.primary_role = 'donor' OR u.primary_role = 'both')"];
        $params = [];

        if (!empty($filters['blood_group_id'])) {
            $where[] = "p.blood_group_id = :blood_group_id";
            $params['blood_group_id'] = (int)$filters['blood_group_id'];
        }

        if (!empty($filters['city'])) {
            $where[] = "p.city = :city";
            $params['city'] = trim($filters['city']);
        }

        if (isset($filters['is_available_donor']) && $filters['is_available_donor'] !== '') {
            $where[] = "p.is_available_donor = :is_available_donor";
            $params['is_available_donor'] = (int)$filters['is_available_donor'];
        }

        $whereClause = implode(" AND ", $where);
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM user_profiles p
            JOIN users u ON p.user_id = u.id
            WHERE {$whereClause}
        ");
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }
}
