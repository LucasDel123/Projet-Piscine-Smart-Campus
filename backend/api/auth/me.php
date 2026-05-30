<?php

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireLogin();

jsonSuccess(Auth::user(), 'Utilisateur connecté.');
