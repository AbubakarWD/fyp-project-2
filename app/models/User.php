<?php
/**
 * BloodLife — User Model
 * Data access queries for user authentication, registration, and role management.
 */

require_once __DIR__ . '/../config/database.php';

class User {

    /**
     * Find user by email address with profile details.
     *
     * @param string $email
     * @return array|null
     */
    public static function findByEmail(string $email): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT u.*, p.full_name, p.phone_number, p.blood_group_id, bg.code as blood_group, 
                   p.city, p.avatar, p.is_available_donor
            FROM users u
            LEFT JOIN user_profiles p ON u.id = p.user_id
            LEFT JOIN blood_groups bg ON p.blood_group_id = bg.id
            WHERE u.email = :email
            LIMIT 1
        ");
        $stmt->execute(['email' => strtolower(trim($email))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by username.
     *
     * @param string $username
     * @return array|null
     */
    public static function findByUsername(string $username): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => strtolower(trim($username))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by ID with profile details.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById(int $id): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT u.*, p.full_name, p.phone_number, p.gender, p.date_of_birth, p.blood_group_id, 
                   bg.code as blood_group, bg.display_name as blood_group_name, p.city, p.state_province, 
                   p.address, p.avatar, p.bio, p.is_available_donor, p.last_donated_at, p.total_donations_count
            FROM users u
            LEFT JOIN user_profiles p ON u.id = p.user_id
            LEFT JOIN blood_groups bg ON p.blood_group_id = bg.id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Register a new user and create associated profile inside a PDO transaction.
     *
     * @param array $data Contains email, username, password_hash, primary_role, full_name, phone_number, blood_group_id, city
     * @return int Created User ID
     * @throws Exception
     */
    public static function register(array $data): int {
        $pdo = Database::getInstance();

        try {
            $pdo->beginTransaction();

            // 1. Insert into users table
            $stmtUser = $pdo->prepare("
                INSERT INTO users (username, email, password, primary_role, status)
                VALUES (:username, :email, :password, :primary_role, 'active')
            ");
            $stmtUser->execute([
                'username'     => strtolower(trim($data['username'])),
                'email'        => strtolower(trim($data['email'])),
                'password'     => $data['password_hash'],
                'primary_role' => $data['primary_role']
            ]);

            $userId = (int)$pdo->lastInsertId();

            // 2. Insert into user_profiles table
            $stmtProfile = $pdo->prepare("
                INSERT INTO user_profiles (user_id, full_name, phone_number, blood_group_id, city, avatar, is_available_donor)
                VALUES (:user_id, :full_name, :phone_number, :blood_group_id, :city, 'default_avatar.png', :is_available_donor)
            ");
            $stmtProfile->execute([
                'user_id'            => $userId,
                'full_name'          => trim($data['full_name']),
                'phone_number'       => trim($data['phone_number']),
                'blood_group_id'     => (int)$data['blood_group_id'],
                'city'               => trim($data['city']),
                'is_available_donor' => ($data['primary_role'] === 'donor' || $data['primary_role'] === 'both') ? 1 : 0
            ]);

            $pdo->commit();
            return $userId;

        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Registration DB Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update user last login timestamp.
     *
     * @param int $id
     */
    public static function updateLastLogin(int $id): void {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Update user password hash.
     *
     * @param int $id
     * @param string $passwordHash
     * @return bool
     */
    public static function updatePassword(int $id, string $passwordHash): bool {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute(['password' => $passwordHash, 'id' => $id]);
    }
}
