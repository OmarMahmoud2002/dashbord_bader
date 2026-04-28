<?php

function do_upload($ci, $inputName, $filename, $uploadPath = './public/uploads/', $allowedTypes = 'gif|jpg|png|jpeg|pdf|doc|docx|xls|xlsx', $maxSize = 5 * 1024 * 1024) {
    $config['upload_path']          = $uploadPath;
    $config['allowed_types']        = $allowedTypes;
    $config['max_size'] = $maxSize;
    $config['file_name'] = $filename;
    $config['overwrite'] = TRUE;

    $ci->load->library('upload', $config);
    if (! $ci->upload->do_upload($inputName)) {
        return ['status' => 'wrong', 'description' => $ci->upload->display_errors()]; 
    } else {
        return ['status' => 'ok', 'data' => $ci->upload->data()];
    }
}

?>