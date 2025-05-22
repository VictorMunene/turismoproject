<?php
// includes/auth.php
class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function isAdmin() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            return $user && $user['is_admin'] == 1;
        } catch (PDOException $e) {
            error_log("Admin check failed: " . $e->getMessage());
            return false;
        }
    }
}
?>