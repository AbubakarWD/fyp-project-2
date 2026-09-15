-- ============================================================================
-- BloodLife — Database Schema (DDL)
-- Production Ready 3NF Normalized Database Schema for MySQL 8.0+ / MariaDB
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `post_interactions`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `conversations`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `donation_records`;
DROP TABLE IF EXISTS `request_responses`;
DROP TABLE IF EXISTS `blood_requests`;
DROP TABLE IF EXISTS `user_profiles`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `blood_groups`;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. Table: blood_groups
-- Master table storing 8 ABO/Rh blood groups & compatibility matrices
-- ----------------------------------------------------------------------------
CREATE TABLE `blood_groups` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(5) NOT NULL UNIQUE,
    `display_name` VARCHAR(20) NOT NULL,
    `can_donate_to` JSON NOT NULL COMMENT 'Array of blood group codes this group can donate to',
    `can_receive_from` JSON NOT NULL COMMENT 'Array of blood group codes this group can receive from',
    `description` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Table: users
-- Core account credentials, role handling ('donor', 'requester', 'both')
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL COMMENT 'BCRYPT hashed password',
    `primary_role` ENUM('donor', 'requester', 'both') NOT NULL DEFAULT 'donor',
    `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    `last_login_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_role` (`primary_role`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Table: user_profiles
-- Extended user profile, location, blood group, donor availability
-- ----------------------------------------------------------------------------
CREATE TABLE `user_profiles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `phone_number` VARCHAR(20) NOT NULL,
    `gender` ENUM('male', 'female', 'other') NOT NULL DEFAULT 'other',
    `date_of_birth` DATE NULL DEFAULT NULL,
    `blood_group_id` INT UNSIGNED NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `state_province` VARCHAR(100) NULL DEFAULT NULL,
    `address` TEXT NULL DEFAULT NULL,
    `avatar` VARCHAR(255) NOT NULL DEFAULT 'default_avatar.png',
    `bio` TEXT NULL DEFAULT NULL,
    `is_available_donor` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = Available, 0 = Unavailable',
    `last_donated_at` DATE NULL DEFAULT NULL,
    `total_donations_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_profiles_blood_group` FOREIGN KEY (`blood_group_id`) REFERENCES `blood_groups` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_profile_search` (`blood_group_id`, `city`, `is_available_donor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. Table: blood_requests
-- Emergency and scheduled blood requests posted by requesters
-- ----------------------------------------------------------------------------
CREATE TABLE `blood_requests` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `requester_id` BIGINT UNSIGNED NOT NULL,
    `patient_name` VARCHAR(100) NOT NULL,
    `blood_group_id` INT UNSIGNED NOT NULL,
    `units_required` INT UNSIGNED NOT NULL DEFAULT 1,
    `units_fulfilled` INT UNSIGNED NOT NULL DEFAULT 0,
    `urgency_level` ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
    `hospital_name` VARCHAR(150) NOT NULL,
    `hospital_address` TEXT NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `contact_number` VARCHAR(20) NOT NULL,
    `required_date` DATE NOT NULL,
    `medical_reason` TEXT NULL DEFAULT NULL,
    `status` ENUM('active', 'partially_fulfilled', 'fulfilled', 'cancelled', 'expired') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_requests_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_requests_blood_group` FOREIGN KEY (`blood_group_id`) REFERENCES `blood_groups` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_request_filtering` (`status`, `urgency_level`, `blood_group_id`, `city`),
    INDEX `idx_request_date` (`required_date`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Table: request_responses
-- Donor willingness responses to specific blood requests
-- ----------------------------------------------------------------------------
CREATE TABLE `request_responses` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `request_id` BIGINT UNSIGNED NOT NULL,
    `donor_id` BIGINT UNSIGNED NOT NULL,
    `units_offered` INT UNSIGNED NOT NULL DEFAULT 1,
    `status` ENUM('pending', 'accepted', 'rejected', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `donor_note` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_responses_request` FOREIGN KEY (`request_id`) REFERENCES `blood_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_responses_donor` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY `uk_donor_request_response` (`request_id`, `donor_id`),
    INDEX `idx_responses_status` (`request_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Table: donation_records
-- Verified log of completed blood donations
-- ----------------------------------------------------------------------------
CREATE TABLE `donation_records` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `donor_id` BIGINT UNSIGNED NOT NULL,
    `request_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `donation_date` DATE NOT NULL,
    `facility_name` VARCHAR(150) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `units_donated` INT UNSIGNED NOT NULL DEFAULT 1,
    `verification_status` ENUM('verified', 'pending', 'rejected') NOT NULL DEFAULT 'verified',
    `notes` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_donations_donor` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_donations_request` FOREIGN KEY (`request_id`) REFERENCES `blood_requests` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_donations_history` (`donor_id`, `donation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. Table: notifications
-- In-app notifications for users
-- ----------------------------------------------------------------------------
CREATE TABLE `notifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(50) NOT NULL COMMENT 'new_response, request_update, donor_match, system',
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `action_link` VARCHAR(255) NULL DEFAULT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_notifications_user_read` (`user_id`, `is_read`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. Table: conversations
-- Thread tracking direct messages between users
-- ----------------------------------------------------------------------------
CREATE TABLE `conversations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user1_id` BIGINT UNSIGNED NOT NULL,
    `user2_id` BIGINT UNSIGNED NOT NULL,
    `request_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `last_message_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_conversations_user1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_conversations_user2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_conversations_request` FOREIGN KEY (`request_id`) REFERENCES `blood_requests` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    UNIQUE KEY `uk_conversations_pair` (`user1_id`, `user2_id`),
    INDEX `idx_conversations_activity` (`last_message_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9. Table: messages
-- Individual messages inside direct message conversations
-- ----------------------------------------------------------------------------
CREATE TABLE `messages` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` BIGINT UNSIGNED NOT NULL,
    `sender_id` BIGINT UNSIGNED NOT NULL,
    `message_text` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_messages_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_messages_thread` (`conversation_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 10. Table: post_interactions
-- Social feed reactions and comments
-- ----------------------------------------------------------------------------
CREATE TABLE `post_interactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `request_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` ENUM('support', 'comment') NOT NULL DEFAULT 'support',
    `comment_text` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_interactions_request` FOREIGN KEY (`request_id`) REFERENCES `blood_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_interactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_interactions_request` (`request_id`, `type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 11. Table: activity_logs
-- Audit trail for key system events
-- ----------------------------------------------------------------------------
CREATE TABLE `activity_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `user_agent` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_logs_user_action` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
