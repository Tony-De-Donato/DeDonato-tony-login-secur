<?php

require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/Database.php";

class PermissionDatabase extends Database {
    public function __construct() {
        parent::__construct();
    }

    // Crée une permission
    public function createPermission($guid, $webServiceId, $permission) {
        $stmt = $this->create('accountauthorization', ['guid' => $guid, 'webservice_id' => $webServiceId, 'permission' => $permission]);
        return $stmt;
    }
}
