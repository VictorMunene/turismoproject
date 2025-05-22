<?php
session_start();
include('includes/dbconnect.php');
include('includes/checklogin.php');
include('includes/auth.php');

$auth = new Auth($pdo);
check_login();

if (!$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

// Get sales data
// Get sales data with proper NULL handling
$totalSold = $pdo->query("SELECT COUNT(*) FROM sales")->fetchColumn() ?? 0;
$totalRevenueResult = $pdo->query("SELECT SUM(sale_price) FROM sales")->fetchColumn();
$totalRevenue = is_null($totalRevenueResult) ? 0 : (float)$totalRevenueResult;

$topSellingModel = $pdo->query("
    SELECT v.make, v.model, COUNT(s.id) as sales_count, SUM(s.sale_price) as total_revenue
    FROM sales s
    JOIN vehicles v ON s.vehicle_id = v.id
    GROUP BY v.make, v.model
    ORDER BY sales_count DESC
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

// Handle empty result for top selling model
if (!$topSellingModel) {
    $topSellingModel = [
        'make' => 'N/A',
        'model' => 'N/A',
        'sales_count' => 0,
        'total_revenue' => 0
    ];
}

$monthlySales = $pdo->query("
    SELECT 
        DATE_FORMAT(sale_date, '%Y-%m') as month,
        COUNT(*) as sales_count,
        SUM(sale_price) as monthly_revenue
    FROM sales
    GROUP BY month
    ORDER BY month DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// If no monthly sales, create demo empty array
if (empty($monthlySales)) {
    $monthlySales = [
        ['month' => date('Y-m'), 'sales_count' => 0, 'monthly_revenue' => 0]
    ];
}

// Demo data for models sold (converted to KES)
$exchangeRate = 130; // Example: 1 USD = 130 KES
$modelsSold = [
    ['make' => 'Toyota', 'model' => 'Harrier', 'count' => 15, 'revenue' => 225000 * $exchangeRate],
    ['make' => 'Mazda', 'model' => 'CX-5', 'count' => 12, 'revenue' => 192000 * $exchangeRate],
    ['make' => 'Ford', 'model' => 'Ranger', 'count' => 8, 'revenue' => 320000 * $exchangeRate],
    ['make' => 'Toyota', 'model' => 'Prado J250', 'count' => 6, 'revenue' => 290000 * $exchangeRate],
    ['make' => 'Volvo', 'model' => 'XC90', 'count' => 5, 'revenue' => 125000 * $exchangeRate],
];

// Function to format KES amounts
function formatKES($amount) {
    return 'KES ' . number_format($amount, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Turismo Motors Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sales-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin-top: 0;
            color: #3498db;
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .chart-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 350px;
        }
        .top-model {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .model-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">
                <h2>Turismo Motors</h2>
                <small>Admin Dashboard</small>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="adminvehicles.php"><i class="fas fa-car"></i> Vehicles</a></li>
                    <li><a href="reports.php"><i class="fas fa-users"></i> Reports</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="test-drives.php"><i class="fas fa-calendar-check"></i> Test Drives</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="header">
                <h1>Sales Reports</h1>
                <div class="user-info">
                    <span>Welcome, Administrator</span>
                </div>
            </header>

            <div class="sales-stats">
                <div class="stat-card">
                    <h3>Total Vehicles Sold</h3>
                    <div class="stat-value"><?php echo $totalSold; ?></div>
                    <p>All-time sales</p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="stat-value"><?php echo formatKES($totalRevenue); ?></div>
                    <p>Gross income from sales</p>
                </div>
                
                <div class="stat-card">
                    <h3>Top Selling Model</h3>
                    <div class="stat-value"><?php echo htmlspecialchars($topSellingModel['make'] . ' ' . $topSellingModel['model']); ?></div>
                    <div class="top-model">
                        <div class="model-detail">
                            <span>Units Sold:</span>
                            <span><?php echo $topSellingModel['sales_count']; ?></span>
                        </div>
                        <div class="model-detail">
                            <span>Revenue:</span>
                            <span><?php echo formatKES($topSellingModel['total_revenue']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-row">
                <div class="chart-container">
                    <h2>Monthly Sales Trend</h2>
                    <canvas id="salesChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h2>Revenue by Model</h2>
                    <canvas id="modelsChart"></canvas>
                </div>
            </div>

            <div class="report-card">
                <h2>Models Sales Performance</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Model</th>
                                <th>Units Sold</th>
                                <th>Total Revenue</th>
                                <th>Avg. Price</th>
                                <th>Market Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modelsSold as $model): 
                                $avgPrice = $model['revenue'] / $model['count'];
                                $marketShare = ($model['count'] / array_sum(array_column($modelsSold, 'count'))) * 100;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($model['make'] . ' ' . $model['model']); ?></td>
                                <td><?php echo $model['count']; ?></td>
                                <td><?php echo formatKES($model['revenue']); ?></td>
                                <td><?php echo formatKES($avgPrice); ?></td>
                                <td>
                                    <div class="progress-bar">
                                        <div style="width: <?php echo $marketShare; ?>%"></div>
                                        <span><?php echo number_format($marketShare, 1); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Monthly sales chart - update tooltip formatting for KES
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthlySales, 'month')); ?>,
                datasets: [{
                    label: 'Units Sold',
                    data: <?php echo json_encode(array_column($monthlySales, 'sales_count')); ?>,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y'
                }, {
                    label: 'Revenue (KES)',
                    data: <?php echo json_encode(array_column($monthlySales, 'monthly_revenue')); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1',
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: KES ' + context.raw.toLocaleString('en-KE');
                            }
                        }
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Units Sold'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Revenue (KES)'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            callback: function(value) {
                                return 'KES ' + value.toLocaleString('en-KE');
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label.includes('Revenue')) {
                                    return label + ': KES ' + context.raw.toLocaleString('en-KE');
                                }
                                return label + ': ' + context.raw;
                            }
                        }
                    }
                }
            }
        });

        // Models revenue chart - update for KES
        const modelsCtx = document.getElementById('modelsChart').getContext('2d');
        const modelsChart = new Chart(modelsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map(function($m) { return $m['make'] . ' ' . $m['model']; }, $modelsSold)); ?>,
                datasets: [{
                    label: 'Revenue (KES)',
                    data: <?php echo json_encode(array_column($modelsSold, 'revenue')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (KES)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'KES ' + (value / 1000) + 'K';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: KES ' + context.raw.toLocaleString('en-KE');
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>