<?php
class ApiResponse {
    public static function success($data, $message = 'Success', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'status'  => 'success',
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ]);
        exit();
    }

    public static function error($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'status'  => 'error',
            'code'    => $code,
            'message' => $message,
            'data'    => null,
        ]);
        exit();
    }

    public static function paginate($data, $total, $page, $limit) {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'code'   => 200,
            'data'   => $data,
            'pagination' => [
                'total'        => $total,
                'per_page'     => $limit,
                'current_page' => $page,
                'total_pages'  => ceil($total / $limit),
            ],
        ]);
        exit();
    }
}