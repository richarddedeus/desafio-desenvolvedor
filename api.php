<?php
declare(strict_types=1);

define('UPLOAD_DIR', __DIR__ . '/uploads');
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

$path = $_SERVER['REQUEST_URI'];

if ($path === '/api.php/upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se o arquivo foi enviado
    if (!isset($_FILES['file'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nenhum arquivo enviado.']);
        exit;
    }

    $file = $_FILES['file'];
    $allowedExtensions = ['csv', 'xlsx'];

    // Valida a extensão do arquivo
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array($fileExtension, $allowedExtensions)) {
        http_response_code(400);
        echo json_encode(['error' => 'Formato de arquivo não suportado.']);
        exit;
    }

    // Verifica se o arquivo já existe
    $destination = UPLOAD_DIR . '/' . basename($file['name']);
    if (file_exists($destination)) {
        http_response_code(400);
        echo json_encode(['error' => 'Arquivo já enviado anteriormente.']);
        exit;
    }

    // Move o arquivo para o diretório de uploads
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        echo json_encode(['success' => 'Arquivo enviado com sucesso.', 'filename' => $file['name']]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao salvar o arquivo.']);
    }
    exit;
}

// Rota não encontrada
http_response_code(404);
echo json_encode(['error' => 'Rota não encontrada.']);
