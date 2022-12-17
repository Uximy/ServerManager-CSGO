<? // Server manager handler
require_once($_SERVER['DOCUMENT_ROOT'] . '/global.php');

$id_server = strip_tags($_POST['server_id']);
$game_mode = strip_tags($_POST['gamemode']);
$sql_tr = "SELECT * FROM servers WHERE server_id=$id_server";
$sql_dz = "SELECT * FROM danger_zone_servers WHERE id=$id_server";
$sql_gg = "SELECT * FROM gun_game_servers WHERE id=$id_server";
$sql_dl = "SELECT * FROM duels_servers WHERE id=$id_server";
$sql_ds = "SELECT * FROM game_servers WHERE id=$id_server";
$result_tr = $connection->query($sql_tr);
$result_dz = $connection->query($sql_dz);
$result_gg = $connection->query($sql_gg);
$result_dl = $connection->query($sql_dl);
$result_ds = $connection->query($sql_ds);
$servers = [];
if ($result_tr->num_rows > 0) {
    foreach ($result_tr as $server) {
        $servers['tournament'] = [
            $server['server_id'] => [json_decode($server['ssh_info'], true)][0]
        ];
    }
}

if ($result_dz->num_rows > 0) {
    foreach ($result_dz as $server) {
        $servers['dangerzone'] = [
            $server['id'] => ["ip" => $server['ssh_ip'],"port" => $server['ssh_port'],"user_name" => $server['ssh_name'],"path" => $server['ssh_path']]
        ];
    }
}

if ($result_gg->num_rows > 0) {
    foreach ($result_gg as $server) {
        $servers['GunGame'] = [
            $server['id'] => ["ip" => $server['ssh_ip'],"port" => $server['ssh_port'],"user_name" => $server['ssh_name'],"path" => $server['ssh_path']]
        ];
    }
}

if ($result_dl->num_rows > 0) {
    foreach ($result_dl as $server) {
        $servers['Duels'] = [
            $server['id'] => ["ip" => $server['ssh_ip'],"port" => $server['ssh_port'],"user_name" => $server['ssh_name'],"path" => $server['ssh_path']]
        ];
    }
}

if ($result_ds->num_rows > 0) {
    foreach ($result_ds as $server) {
        $servers['GameServer'] = [
            $server['id'] => ["ip" => $server['ssh_ip'],"port" => $server['ssh_port'],"user_name" => $server['ssh_name'],"path" => $server['ssh_path']]
        ];
    }
}



if (isset($_POST['request']) && $_POST['request'] == 'start_server') {
    $server = $servers[$game_mode][$id_server];

    if (str_replace('\n', '', function_exists('ssh2_connect')) == true) {
        $sshConnection = ssh2_connect($server['ip'], $server['port']);
        $authResult = ssh2_auth_password($sshConnection, $server['user_name'], 'password');

        if ($authResult === true) {
            $stream = ssh2_exec($sshConnection, './csgoserver start');

            $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            stream_set_blocking($errorStream, true);
            stream_set_blocking($stream, true);
            $streamContent = stream_get_contents($stream);
            $output = $errorStreamContent . "\n" . $streamContent;

            echo json_encode($output);
        } else {
            echo json_encode('error_with_connection');
        }
    }
}

if (isset($_POST['request']) && $_POST['request'] == 'stop_server') {
    $server = $servers[$game_mode][$id_server];

    if (str_replace('\n', '', function_exists('ssh2_connect')) == true) {
        $sshConnection = ssh2_connect($server['ip'], $server['port']);
        $authResult = ssh2_auth_password($sshConnection, $server['user_name'], 'password');

        if ($authResult === true) {
            $stream = ssh2_exec($sshConnection, './csgoserver stop');

            $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            stream_set_blocking($errorStream, true);
            stream_set_blocking($stream, true);
            $streamContent = stream_get_contents($stream);
            $output = $errorStreamContent . "\n" . $streamContent;

            echo json_encode($output + 'я останавливаю сервер');
        } else {
            echo json_encode('error_with_connection');
        }
    }
}

if (isset($_POST['request']) && $_POST['request'] == 'restart_server') {
    $server = $servers[$game_mode][$id_server];

    if (str_replace('\n', '', function_exists('ssh2_connect')) == true) {
        $sshConnection = ssh2_connect($server['ip'], $server['port']);
        $authResult = ssh2_auth_password($sshConnection, $server['user_name'], 'password');

        if ($authResult === true) {
            $stream = ssh2_exec($sshConnection, './csgoserver restart');

            $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            stream_set_blocking($errorStream, true);
            stream_set_blocking($stream, true);
            $streamContent = stream_get_contents($stream);
            $output = $errorStreamContent . "\n" . $streamContent;

            echo json_encode($output);
        } else {
            echo json_encode('error_with_connection');
        }
    }
}

if (isset($_POST['request']) && $_POST['request'] == 'status_server') {
    $server = $servers[$game_mode][$id_server];

    if (str_replace('\n', '', function_exists('ssh2_connect')) == true) {
        $sshConnection = ssh2_connect($server['ip'], $server['port']);
        $authResult = ssh2_auth_password($sshConnection, $server['user_name'], 'password');

        if ($authResult === true) {
            $stream = ssh2_exec($sshConnection, './csgoserver dt');

            $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            stream_set_blocking($errorStream, true);
            stream_set_blocking($stream, true);
            $streamContent = stream_get_contents($stream);
            $output = $errorStreamContent."\n".$streamContent;

            echo json_encode($output);
        } else {
            echo json_encode('error_with_connection');
        }
    }
}

if (isset($_POST['request']) && $_POST['request'] == 'update_server') {
    $server = $servers[$game_mode][$id_server];

    if (str_replace('\n', '', function_exists('ssh2_connect')) == true) {
        $sshConnection = ssh2_connect($server['ip'], $server['port']);
        $authResult = ssh2_auth_password($sshConnection, $server['user_name'], 'password');

        if ($authResult === true) {
            $stream = ssh2_exec($sshConnection, './csgoserver cu');
            
            $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            stream_set_blocking($errorStream, true);
            stream_set_blocking($stream, true);
            $streamContent = stream_get_contents($stream);
            $output = $errorStreamContent . "\n" . $streamContent;

            preg_match('/(Local build): \D+[0-9]{2}m([0-9]*)/', $output, $local_build);
            preg_match('/(Remote build:) \D+[0-9]{2}m([0-9]*)/', $output, $remote_build);

            // print_r($local_build[2]);

            if ($local_build[2] !== $remote_build[2]) {
                $stream = ssh2_exec($sshConnection, './csgoserver stop');

                $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
                stream_set_blocking($errorStream, true);
                stream_set_blocking($stream, true);
                $streamContent = stream_get_contents($stream);
                $output = $errorStreamContent . "\n" . $streamContent + 'я останавливаю сервер';

                if ($output == 0) {
                    $stream = ssh2_exec($sshConnection, './csgoserver update');

                    $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
                    stream_set_blocking($errorStream, true);
                    stream_set_blocking($stream, true);
                    $streamContent = stream_get_contents($stream);
                    $output = $errorStreamContent . "\n" . $streamContent;

                    preg_match('/\D+[0-9]{2}m(Complete)/',$output , $complete);
                    
                    if ($complete[1] == 'Complete') {
                        $stream = ssh2_exec($sshConnection, './csgoserver start');

                        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
                        stream_set_blocking($errorStream, true);
                        stream_set_blocking($stream, true);
                        $streamContent = stream_get_contents($stream);
                    }
                    echo json_encode($output);
                }
            }else{
                echo json_encode($output);
            }

        } else {
            echo json_encode('error_with_connection');
        }
    }
}

if (isset($_POST['request']) && $_POST['request'] == 'check_update_server') {
    $server = $servers[$game_mode][$id_server];

    if (str_replace('\n', '', function_exists('ssh2_connect')) == true) {
        $sshConnection = ssh2_connect($server['ip'], $server['port']);
        $authResult = ssh2_auth_password($sshConnection, $server['user_name'], 'password');

        if ($authResult === true) {
            $stream = ssh2_exec($sshConnection, './csgoserver cu');

            $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            stream_set_blocking($errorStream, true);
            stream_set_blocking($stream, true);
            $streamContent = stream_get_contents($stream);
            $output = $errorStreamContent . "\n" . $streamContent;

            echo json_encode($output);
        } else {
            echo json_encode('error_with_connection');
        }
    }
}
