<?php
session_start();
unset($_SESSION['whatsapp_url']);
echo json_encode(['success' => true]);
?>