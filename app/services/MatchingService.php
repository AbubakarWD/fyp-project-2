<?php
/**
 * BloodLife — Matching Service
 * Intelligent ABO compatibility matching engine and automated notification dispatch.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/BloodGroup.php';
require_once __DIR__ . '/../models/BloodRequest.php';
require_once __DIR__ . '/../models/Notification.php';

class MatchingService {

    /**
     * Find matching donors based on ABO compatibility rules and city location.
     *
     * @param string $recipientBloodCode E.g. 'A+', 'O-'
     * @param string|null $city E.g. 'Karachi'
     * @param bool $availableOnly
     * @return array Array of matching donor profiles
     */
    public static function findMatchingDonors(string $recipientBloodCode, ?string $city = null, bool $availableOnly = true): array {
        $compatibleDonorGroupIds = BloodGroup::getCompatibleDonorGroupIds($recipientBloodCode);
        if (empty($compatibleDonorGroupIds)) {
            return [];
        }

        $pdo = Database::getInstance();
        $placeholders = implode(',', array_fill(0, count($compatibleDonorGroupIds), '?'));
        
        $where = [
            "u.status = 'active'",
            "(u.primary_role = 'donor' OR u.primary_role = 'both')",
            "p.blood_group_id IN ({$placeholders})"
        ];
        $params = $compatibleDonorGroupIds;

        if ($availableOnly) {
            $where[] = "p.is_available_donor = 1";
        }

        if (!empty($city)) {
            $where[] = "p.city = ?";
            $params[] = trim($city);
        }

        $whereClause = implode(" AND ", $where);

        $sql = "
            SELECT p.*, u.email, u.username, bg.code as blood_group
            FROM user_profiles p
            JOIN users u ON p.user_id = u.id
            JOIN blood_groups bg ON p.blood_group_id = bg.id
            WHERE {$whereClause}
            ORDER BY p.total_donations_count DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Notify all matching donors when an emergency blood request is created.
     *
     * @param int $requestId
     * @return int Count of notified donors
     */
    public static function notifyMatchingDonors(int $requestId): int {
        $request = BloodRequest::findById($requestId);
        if (!$request) {
            return 0;
        }

        $matchingDonors = self::findMatchingDonors($request['blood_group'], $request['city'], true);
        $notifiedCount = 0;

        foreach ($matchingDonors as $donor) {
            // Avoid notifying the requester if they are also a donor
            if ((int)$donor['user_id'] === (int)$request['requester_id']) {
                continue;
            }

            $title = "Urgent " . $request['blood_group'] . " Blood Request in " . $request['city'];
            $message = "Emergency blood request created for patient " . $request['patient_name'] . " at " . $request['hospital_name'] . ". Can you help?";
            $link = "/requests.php?id=" . $requestId;

            Notification::create($donor['user_id'], 'donor_match', $title, $message, $link);
            $notifiedCount++;
        }

        return $notifiedCount;
    }
}
