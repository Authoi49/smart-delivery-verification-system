-- ============================================
-- Migration: Add Live Location Tracking
-- ============================================
-- Run this in phpMyAdmin's SQL tab (select your smart_delivery database first)
-- AFTER you've already imported database.sql.
-- This just ADDS new columns - it won't affect your existing data.

USE smart_delivery;

ALTER TABLE orders
    ADD COLUMN rider_lat DECIMAL(10, 7) DEFAULT NULL,
    ADD COLUMN rider_lng DECIMAL(10, 7) DEFAULT NULL,
    ADD COLUMN location_updated_at TIMESTAMP NULL DEFAULT NULL;
