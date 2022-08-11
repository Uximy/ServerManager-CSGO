<? // Server manager
$pageTitle = 'Управление серверами';
$currentPage = 'server_manager';
require_once($_SERVER['DOCUMENT_ROOT'] . '/global.php');
// if(!is_admin()) {} // проверка является ли он админом
// echo '<pre>';
// var_dump($logged_user); // вызов переменной логера
// echo '</pre>';
if (!$logged_user['uid'] == 2 || !$logged_user['uid'] == 3 || !$logged_user['uid'] == 38264 || !$logged_user['uid'] == 38161) {
    echo '<div class="centered">' . $lang_Const['tournaments_titile_NotEnough'] . '</div>';
    exit;
}

?>
<script>
    set_page('Server Manager', 'server_manager');
</script>
<div class="Manager_Server">
    <div id="servers_manage">
            <input id="server-Tournament-toggler" checked type="radio" name="profile-menu" class="hidden">
            <input id="server-DangerZone-toggler" type="radio" name="profile-menu" class="hidden">
            <input id="server-Duels-toggler" type="radio" name="profile-menu" class="hidden">
            <input id="server-GunGame-toggler" type="radio" name="profile-menu" class="hidden">
            <input id="server-GameServers-toggler" type="radio" name="profile-menu" class="hidden">
            <div id="server_manager_menu">
                <label for="server-Tournament-toggler">Турнирные Cервера</label>
                <label for="server-DangerZone-toggler">DZ Сервера</label>
                <label for="server-Duels-toggler">Duels Сервера</label>
                <label for="server-GunGame-toggler">GG Сервера</label>
                <label for="server-GameServers-toggler">Игровые Сервера</label>
            </div>
        <?
        if (is_admin()) {
            $sql = "SELECT matches.first_team_name, matches.second_team_name, matches.tournament_id , servers.server_id, servers.server_ip, servers.status, servers.game_id FROM matches, servers WHERE servers.game_type = 'match' AND servers.game_id = matches.match_id";
            $busy = $connection->query($sql);
            $sql2 = "SELECT servers.server_id, servers.server_ip, servers.status FROM servers WHERE status = 'free' ";
            $free = $connection->query($sql2);
            $arr = [];

            $DZ_Servers = $connection->query('SELECT * FROM danger_zone_servers');

            $DL_Servers = $connection->query('SELECT * FROM duels_servers');

            $GG_Servers = $connection->query('SELECT * FROM gun_game_servers');

            $Game_Servers = $connection->query('SELECT * FROM game_servers');


            $arr['tournament'] = [];
            $arr['DZ'] = [];
            $arr['DL'] = [];
            $arr['GG'] = [];
            $arr['GS'] = [];

            foreach ($busy as $key => $value) {
                array_push($arr['tournament'], $value);
            }
            foreach ($free as $key => $value) {
                array_push($arr['tournament'], $value);
            }

            foreach ($DZ_Servers as $key => $value) {
                array_push($arr['DZ'], $value);
            }

            foreach ($DL_Servers as $key => $value) {
                array_push($arr['DL'], $value);
            }

            foreach ($GG_Servers as $key => $value) {
                array_push($arr['GG'], $value);
            }   

            foreach ($Game_Servers as $key => $value) {
                array_push($arr['GS'], $value);
            }

            $responce_server = 'На данный момент ответа от сервера нет';
            //? Tournaments Сервера
            if (@$result->num_rows > 0) {
                echo '
                    <table id="server-Tournament-toggler" class="manager-box Tournament table_servers">
                        <tr>
                            <td>ID</td>
                            <td>IP Сервера</td>
                            <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                            <td class="tournament_server_status">Ответ сервера</td>
                            <td>Матчи</td>
                            <td>'.$lang_Const['Duels_title_Action'].'</td>
                        </tr>';


                foreach ($arr['tournament'] as $key) {
                    if ($key['status'] == 'free') {
                        $server_status = [$lang_Const['Duels_title_Available'], 'status_Busy' ];
                    } else if ($key['status'] == 'busy') {
                        $server_status = [$lang_Const['Duels_title_Busy'], 'status_Available'];
                    }

                    if ($key['game_id'] != '') {
                        $current_match = '<a class="good is_on no_hover" href="/tournaments/?show_tournament=' . $key['tournament_id'] . '">' . $key['first_team_name'] . ' VS ' . $key['second_team_name'] . '</a>';
                        $classes_for_table = 'good is_on';
                    } else {
                        $current_match = $lang_Const['Duels_title_noMatches'];
                        $classes_for_table = '';
                    }

                    echo '
                                <tr class="one_tournament_server">
                                    <td class="tournament_server_id">' . $key['server_id'] . '</td>
                                    <td class="tournament_server_ip">' . $key['server_ip'] . '</td>
                                    <td class="tournament_server_status '.$server_status[1].'">' . $server_status[0] . '</td>
                                    <td class="tournament_server_responce" id="tournament_' . $key['server_id'] . '">' . $responce_server . '</td>
                                    <td class="tournament_server_current_game ' . $classes_for_table . '" style="width: 14%;">' . $current_match . '</td>
                                    <td class="tournament_server_id_options manager_btn">
                                        <div class="btn make_server_free center_btn" onclick="start_server(' . $key['server_id'] . ', '."1".')">Запуск</div>
                                        <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['server_id'] . ', '."1".')">' . $lang_Const['Duels_title_switchOff'] . '</div>
                                        <div class="btn make_server_busy center_btn" onclick="restart_server(' . $key['server_id'] . ', '."1".')">Рестарт</div>
                                        <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['server_id'] . ', '."1".')">Статус</div>
                                        <div class="btn make_server_busy center_btn" style="width: 30%;padding: 10px 0;" onclick="check_update_server(' . $key['server_id'] . ', '."1".')">Проверить Обновление</div>
                                        <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['server_id'] . ', '."1".')">Обновить</div>
                                    </td>
                                </tr>
                                ';
                }
                echo '</table>';
            } else {
                echo '<div class="center">Ошибка с получением данных серверов.</div>';
            }


            //? Danger-Zone Сервера
            if (@$result->num_rows > 0) {
                echo '
                    <table id="server-DangerZone-toggler" class="manager-box DangerZone table_servers">
                        <tr>
                            <td>ID</td>
                            <td>IP Сервера</td>
                            <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                            <td class="tournament_server_status">Ответ сервера</td>
                            <td>'.$lang_Const['Duels_title_Action'].'</td>
                        </tr>';
                                // <td>Локация</td>

                foreach ($arr['DZ'] as $key) {
                    if ($key['status'] == 0) {
                        $server_status_dz = [$lang_Const['Duels_title_Available'], 'status_Busy' ];
                    } else if ($key['status'] == 1) {
                        $server_status_dz = [$lang_Const['Duels_title_Busy'], 'status_Available'];
                    }
                    
                    echo '
                                <tr class="one_tournament_server" id="danger-zone">
                                    <td class="tournament_server_id">' . $key['id'] . '</td>
                                    <td class="tournament_server_ip">' . $key['server_ip'] . '</td>
                                    <td class="tournament_server_status '.$server_status_dz[1].'">' . $server_status_dz[0] . '</td>
                                    <td class="tournament_server_responce" id="dangerzone_' . $key['id'] . '">' . $responce_server . '</td>
                                    <td class="tournament_server_id_options manager_btn">
                                        <div class="btn make_server_busy center_btn" onclick="start_server(' . $key['id'] . ', '."2".')">Запуск</div>
                                        <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['id'] . ', '."2".')">' . $lang_Const['Duels_title_switchOff'] . '</div>
                                        <div class="btn make_server_busy center_btn" id="manager_command" data-mode="DangerZone" onclick="restart_server(' . $key['id'] . ')">Рестарт</div>
                                        <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['id'] .', '."2".')">Статус</div>
                                        <div class="btn make_server_busy center_btn" style="width: 25%;padding: 10px 0;" onclick="check_update_server(' . $key['id'] . ', '."2".')">Проверить Обновление</div>
                                        <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['id'] . ', '."2".')">Обновить</div>
                                    </td>
                                </tr>
                                ';
                                //<td class="tournament_server_current_game ' . $classes_for_table . '" style="width: 14%;">'. $key['location'] .'</td>
                }
                echo '</table>';
            } else {
                echo '<div class="center">Ошибка с получением данных серверов.</div>';
            }

            //? Duels Сервера
            if (@$result->num_rows > 0) {
                echo '
                            <table id="server-Duels-toggler" class="manager-box Duels table_servers">
                                <tr>
                                    <td>ID</td>
                                    <td>IP Сервера</td>
                                    <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                                    <td class="tournament_server_status">Ответ сервера</td>
                                    <td>Локация</td>
                                    <td>'.$lang_Const['Duels_title_Action'].'</td>
                                </tr>';


                foreach ($arr['DL'] as $key) {
                    if ($key['status'] == 0) {
                        $server_status_dz = [$lang_Const['Duels_title_Available'], 'status_Busy' ];
                    } else if ($key['status'] == 1) {
                        $server_status_dz = [$lang_Const['Duels_title_Busy'], 'status_Available'];
                    }

                    echo '
                                <tr class="one_tournament_server">
                                    <td class="tournament_server_id">' . $key['id'] . '</td>
                                    <td class="tournament_server_ip">' . $key['server_ip'] . '</td>
                                    <td class="tournament_server_status '.$server_status_dz[1].'">' . $server_status_dz[0] . '</td>
                                    <td class="tournament_server_responce" id="Duels_' . $key['id'] . '">' . $responce_server . '</td>
                                    <td class="tournament_server_current_game ' . $classes_for_table . '" style="width: 14%;">'. $key['location'] .'</td>
                                    <td class="tournament_server_id_options manager_btn">
                                        <div class="btn make_server_free center_btn" onclick="start_server(' . $key['id'] . ', '."3".')">Запуск</div>
                                        <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['id'] . ', '."3".')">' . $lang_Const['Duels_title_switchOff'] . '</div>
                                        <div class="btn make_server_busy center_btn" onclick="restart_server(' . $key['id'] . ', '."3".')">Рестарт</div>
                                        <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['id'] . ', '."3".')">Статус</div>
                                        <div class="btn make_server_busy center_btn" style="width: 30%;padding: 10px 0;" onclick="check_update_server(' . $key['id'] . ', '."3".')">Проверить Обновление</div>
                                        <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['id'] . ', '."3".')">Обновить</div>
                                    </td>
                                </tr>
                                ';
                }
                echo '</table>';
            } else {
                echo '<div class="center">Ошибка с получением данных серверов.</div>';
            }

            //? GunGame Сервера
            if (@$result->num_rows > 0) {
                echo '
                            <table id="server-GunGame-toggler" class="manager-box GunGame table_servers">
                                <tr>
                                    <td>ID</td>
                                    <td>IP Сервера</td>
                                    <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                                    <td class="tournament_server_status">Ответ сервера</td>
                                    <td>'.$lang_Const['Duels_title_Action'].'</td>
                                </tr>';


                foreach ($arr['GG'] as $key) {
                    if ($key['status'] == 0) {
                        $server_status_gg = [$lang_Const['Duels_title_Available'], 'status_Busy' ];
                    } else if ($key['status'] == 1) {
                        $server_status_gg = [$lang_Const['Duels_title_Busy'], 'status_Available'];
                    }

                    echo '
                                <tr class="one_tournament_server">
                                    <td class="tournament_server_id">' . $key['id'] . '</td>
                                    <td class="tournament_server_ip">' . $key['server_ip'] . '</td>
                                    <td class="tournament_server_status '.$server_status_gg[1].'">' . $server_status_gg[0] . '</td>
                                    <td class="tournament_server_responce" id="GunGame_' . $key['id'] . '">' . $responce_server . '</td>
                                    <td class="tournament_server_id_options manager_btn">
                                        <div class="btn make_server_free center_btn" onclick="start_server(' . $key['id'] . ', '."4".')">Запуск</div>
                                        <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['id'] . ', '."4".')">' . $lang_Const['Duels_title_switchOff'] . '</div>
                                        <div class="btn make_server_busy center_btn" onclick="restart_server(' . $key['id'] . ', '."4".')">Рестарт</div>
                                        <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['id'] . ', '."4".')">Статус</div>
                                        <div class="btn make_server_busy center_btn" style="width: 25%;padding: 10px 0;" onclick="check_update_server(' . $key['id'] . ', '."4".')">Проверить Обновление</div>
                                        <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['id'] . ', '."4".')">Обновить</div>
                                    </td>
                                </tr>
                                ';
                }
                echo '</table>';
            } else {
                echo '<div class="center">Ошибка с получением данных серверов.</div>';
            }

            //? Game Сервера
            if (@$result->num_rows > 0) {
                echo '
                            <table id="server-GunGame-toggler" class="manager-box GameServers table_servers">
                                <tr>
                                    <td>ID</td>
                                    <td>IP Сервера</td>
                                    <td>Категория</td>
                                    <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                                    <td class="tournament_server_status">Ответ сервера</td>
                                    <td>'.$lang_Const['Duels_title_Action'].'</td>
                                </tr>';


                foreach ($arr['GS'] as $key) {
                    if ($key['status'] == 0) {
                        $server_status_dz = [$lang_Const['Duels_title_Available'], 'status_Busy' ];
                    } else if ($key['status'] == 1) {
                        $server_status_dz = [$lang_Const['Duels_title_Busy'], 'status_Available'];
                    }

                    echo '
                                <tr class="one_tournament_server">
                                    <td class="tournament_server_id">' . $key['id'] . '</td>
                                    <td class="tournament_server_ip">' . $key['ip'] . '</td>
                                    <td class="tournament_server_category">' . $key['name'] . '</td>
                                    <td class="tournament_server_status '.$server_status_dz[1].'">' . $server_status_dz[0] . '</td>
                                    <td class="tournament_server_responce" id="GameServer_' . $key['id'] . '">' . $responce_server . '</td>
                                    <td class="tournament_server_id_options manager_btn">
                                        <div class="btn make_server_free center_btn" onclick="start_server(' . $key['id'] . ', '."5".')">Запуск</div>
                                        <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['id'] . ', '."5".')">' . $lang_Const['Duels_title_switchOff'] . '</div>
                                        <div class="btn make_server_busy center_btn" onclick="restart_server(' . $key['id'] . ', '."5".')">Рестарт</div>
                                        <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['id'] . ', '."5".')">Статус</div>
                                        <div class="btn make_server_busy center_btn" style="width: 27%;padding: 10px 0;" onclick="check_update_server(' . $key['id'] . ', '."5".')">Проверить Обновление</div>
                                        <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['id'] . ', '."5".')">Обновить</div>
                                    </td>
                                </tr>
                                ';
                }
            echo '</table>';
                
            } else {
                echo '<div class="center">Ошибка с получением данных серверов.</div>';
            }
        }
        ?>
    </div>
</div>

<script src="../jQuery.js" defer></script>
<script src="/lang.js?v=1.3" defer></script>
<script src="server_manager.js?v=1.2" defer></script>