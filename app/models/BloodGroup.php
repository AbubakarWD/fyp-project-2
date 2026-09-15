<?php
/**
 * BloodLife — BloodGroup Model
 * Data access and ABO/Rh blood group compatibility rules engine.
 */

require_once __DIR__ . '/../config/database.php';

class BloodGroup {

    /**
     * Retrieve all 8 blood groups ordered by code.
     *
     * @return array
     */
    public static function getAll(): array {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM blood_groups ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Find blood group by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM blood_groups WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find blood group by code (e.g., 'A+', 'O-').
     *
     * @param string $code
     * @return array|null
     */
    public static function findByCode(string $code): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM blood_groups WHERE code = :code LIMIT 1");
        $stmt->execute(['code' => strtoupper(trim($code))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get list of blood group IDs that can donate to a recipient blood group.
     *
     * @param string $recipientCode E.g. 'A+'
     * @return array Array of blood_group_id integers
     */
    public static function getCompatibleDonorGroupIds(string $recipientCode): array {
        $recipientCode = strtoupper(trim($recipientCode));
        $all = self::getAll();
        $compatibleIds = [];

        foreach ($all as $bg) {
            $canDonateTo = json_decode($bg['can_donate_to'], true) ?: [];
            if (in_array($recipientCode, $canDonateTo)) {
                $compatibleIds[] = (int)$bg['id'];
            }
        }

        return $compatibleIds;
    }
}
