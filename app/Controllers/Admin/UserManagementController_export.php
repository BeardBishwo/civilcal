    /**
     * Export login logs to CSV
     */
    public function exportLoginLogs()
    {
        // Check admin authentication
        if (!$this->auth->isAdmin()) {
            header('Location: ' . app_base_url('/login'));
            exit;
        }

        // Get filter parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $country = isset($_GET['country']) ? $_GET['country'] : '';
        
        $where = [];
        $params = [];

        if ($search) {
            $where[] = "(u.username LIKE ? OR u.email LIKE ? OR ls.ip_address LIKE ? OR ls.city LIKE ? OR ls.country LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
        }

        if ($startDate) {
            $where[] = "ls.login_time >= ?";
            $params[] = $startDate . ' 00:00:00';
        }

        if ($endDate) {
            $where[] = "ls.login_time <= ?";
            $params[] = $endDate . ' 23:59:59';
        }

        if ($country) {
            $where[] = "ls.country = ?";
            $params[] = $country;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Fetch data
        $sql = "SELECT 
                    ls.id,
                    u.username,
                    u.email,
                    ls.ip_address,
                    ls.country,
                    ls.region,
                    ls.city,
                    ls.timezone,
                    ls.device_type,
                    ls.browser,
                    ls.os,
                    ls.login_time
                FROM login_sessions ls
                LEFT JOIN users u ON ls.user_id = u.id
                $whereSql
                ORDER BY ls.login_time DESC
                LIMIT 10000";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="login_logs_' . date('Y-m-d_His') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 support
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Add CSV headers
        fputcsv($output, [
            'ID',
            'Username',
            'Email',
            'IP Address',
            'Country',
            'Region',
            'City',
            'Timezone',
            'Device Type',
            'Browser',
            'OS',
            'Login Time'
        ]);

        // Add data rows
        foreach ($logs as $log) {
            $row = [
                $log['id'],
                $log['username'] ?? 'N/A',
                $log['email'] ?? 'N/A',
                $log['ip_address'] ?? 'N/A',
                $log['country'] ?? 'Unknown',
                $log['region'] ?? 'Unknown',
                $log['city'] ?? 'Unknown',
                $log['timezone'] ?? 'N/A',
                $log['device_type'] ?? 'Unknown',
                $log['browser'] ?? 'Unknown',
                $log['os'] ?? 'Unknown',
                $log['login_time']
            ];

            // CSV Injection Protection: prepend tab if value starts with trigger characters
            foreach ($row as &$value) {
                if ($value !== null && strlen($value) > 0) {
                    $firstChar = $value[0];
                    if (in_array($firstChar, ['=', '+', '-', '@'])) {
                        $value = "\t" . $value;
                    }
                }
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
