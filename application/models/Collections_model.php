<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Collections_model extends CI_Model
{
    protected $table = 'bookings';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_weekly()
    {
        $sql = "SELECT id, customer_name, device_type, device_model, fault_description, date_booked, date_ready, collected_date, amount_charged, payment_status
                FROM {$this->table}
                WHERE STATUS = 'Collected' AND YEARWEEK(collected_date, 1) = YEARWEEK(CURDATE(), 1)
                ORDER BY collected_date DESC";
        $q = $this->db->query($sql);
        return $q->result_array();
    }

    public function get_monthly()
    {
        $sql = "SELECT id, customer_name, device_type, device_model, fault_description, date_booked, date_ready, collected_date, amount_charged, payment_status
                FROM {$this->table}
                WHERE STATUS = 'Collected' AND MONTH(collected_date) = MONTH(CURDATE()) AND YEAR(collected_date) = YEAR(CURDATE())
                ORDER BY collected_date DESC";
        $q = $this->db->query($sql);
        return $q->result_array();
    }

    public function get_weekly_profits()
    {
        $sql = "SELECT COUNT(*) AS total_devices, COALESCE(SUM(amount_charged),0) AS total_revenue
                FROM {$this->table}
                WHERE STATUS = 'Collected' AND YEARWEEK(collected_date, 1) = YEARWEEK(CURDATE(), 1)";
        $q = $this->db->query($sql);
        return $q->row_array();
    }

    public function get_monthly_profits()
    {
        $sql = "SELECT COUNT(*) AS total_devices, COALESCE(SUM(amount_charged),0) AS total_revenue
                FROM {$this->table}
                WHERE STATUS = 'Collected' AND MONTH(collected_date) = MONTH(CURDATE()) AND YEAR(collected_date) = YEAR(CURDATE())";
        $q = $this->db->query($sql);
        return $q->row_array();
    }
}
