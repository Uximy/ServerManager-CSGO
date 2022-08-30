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
            $arr = [];
            $arr['DZ'] = [];
            $arr['DL'] = [];
            $arr['GG'] = [];
            $arr['GS'] = [];

            $DZ_Servers = $connection->query('SELECT * FROM danger_zone_servers');

            $DL_Servers = $connection->query('SELECT * FROM duels_servers');

            $GG_Servers = $connection->query('SELECT * FROM gun_game_servers');

            $Game_Servers = $connection->query('SELECT * FROM game_servers');

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

            $responce_server = $lang_Const['Server_Manager_table_header_noResponce'];

            //? Tournaments Сервера

            $sql = "SELECT servers.server_id, servers.server_ip, servers.status, servers.location, servers.game_id, servers.game_type FROM servers";
            $all_servers = $connection->query($sql);

            $match_ids = [];


            foreach ($all_servers as $value) {
                if ($value['game_type'] == 'match' && $value['game_id'] !== null) {
                    $match_ids[] = $value['game_id'];
                }
            }

            $match_ids = implode(',', $match_ids);

            $sql = "SELECT matches.match_id, matches.first_team_name, matches.second_team_name, matches.tournament_id FROM matches WHERE matches.match_id IN($match_ids) AND matches.match_status = 'is_on'";
            $result = $connection->query($sql);

            $matches = [];
            foreach ($result as $key) {
                $matches[$key['match_id']] = $key;
            }

            //if ($logged_user['uid'] == 38264) {
                // echo '<pre>';
                // var_dump($match_ids); // вывод список всех id match
                // echo '</pre>';

                // echo '<pre>';
                // var_dump($matches); // вывод список всех id match
                // echo '</pre>';

                // echo '<pre>';
                // foreach ($all_servers as $value) {
                //     var_dump($value); // вывод список всех id match
                // }
                // echo '</pre>';

                // echo '<pre>';
                // var_dump($all_servers); // вывод список всех id match
                // echo '</pre>';
            //}

            if (@$all_servers->num_rows > 0) {
                echo '
                <div class="manager-box tournament_location"><div class="location">Russian</div></div>
                    <table id="server-Tournament-toggler" class="manager-box Tournament table_servers" style="border-radius: 0 0 10px 10px;">
                        <tr>
                            <td>ID</td>
                            <td>'.$lang_Const['Server_Manager_table_header_ipServer_title'].'</td>
                            <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                            <td class="tournament_server_status">'.$lang_Const['Server_Manager_table_header_responce_title'].'</td>
                            <td>'.$lang_Const['Server_Manager_table_header_match_title'].'</td>
                            <td>'.$lang_Const['Duels_title_Action'].'</td>
                        </tr>
                        ';


                foreach ($all_servers as $key) {
                    if ($key['status'] == 'free') {
                        $server_status = [$lang_Const['Duels_title_Available'], 'status_Busy' ];
                    } else if ($key['status'] == 'busy') {
                        $server_status = [$lang_Const['Duels_title_Busy'], 'status_Available'];
                    }

                    if($key['game_type'] == 'match' && $key['game_id'] !== null && $matches[$key['game_id']]['match_id'] !== null) {
                        // echo '<pre>';
                        // var_dump($matches[$server['game_id']]);
                        // echo '</pre>';
                        $current_match = '<a class="good is_on no_hover" target="_blank" href="/tournaments/?show_tournament=' . $matches[$key['game_id']]['tournament_id'] . '">' . $matches[$key['game_id']]['first_team_name'] . ' VS ' . $matches[$key['game_id']]['second_team_name'] . '</a>';
                        $classes_for_table = 'good is_on';
                    }else {
                        $current_match = $lang_Const['Duels_title_noMatches'];
                        $classes_for_table = '';
                    }
                    
                    if ($key['location'] == 'ru') {
                        echo '
                            <tr class="one_tournament_server">
                                <td class="tournament_server_id">' . $key['server_id'] . '</td>
                                <td class="tournament_server_ip">' . $key['server_ip'] . '</td>
                                <td class="tournament_server_status '.$server_status[1].'">' . $server_status[0] . '</td>
                                <td class="tournament_server_responce" id="tournament_' . $key['server_id'] . '">' . $responce_server . '</td>
                                <td class="tournament_server_current_game ' . $classes_for_table . '" style="width: 14%;">' . $current_match . '</td>
                                <td class="tournament_server_id_options manager_btn">
                                    <div class="btn make_server_free center_btn" onclick="start_server(' . $key['server_id'] . ', '."1".')">Start</div>
                                    <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['server_id'] . ', '."1".')">Turn off</div>
                                    <div class="btn make_server_busy center_btn" onclick="restart_server(' . $key['server_id'] . ', '."1".')">Restart</div>
                                    <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['server_id'] . ', '."1".')">Status</div>
                                    <div class="btn make_server_busy center_btn" style="width: 6.5rem;padding: 10px 0;" onclick="check_update_server(' . $key['server_id'] . ', '."1".')">Check Update</div>
                                    <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['server_id'] . ', '."1".')">Update</div>
                                </td>
                            </tr>
                        ';
                    }
                }
                echo '
                </table>
                <table style="margin-top: 25px;" id="server-Tournament-toggler" class="manager-box Tournament table_servers">
                <tr>
                    <td colspan="10" style="padding: 0!important;" class="tournament_location"><div class="location">Europe</div></td>
                </tr>
                ';
                foreach ($all_servers as $key) {
                    if ($key['status'] == 'free') {
                        $server_status = [$lang_Const['Duels_title_Available'], 'status_Busy' ];
                    } else if ($key['status'] == 'busy') {
                        $server_status = [$lang_Const['Duels_title_Busy'], 'status_Available'];
                    }

                    if ($key['game_type'] == 'match' && $key['game_id'] !== null && $matches[$key['game_id']]['match_id'] !== null) {
                        $current_match = '<a class="good is_on no_hover" target="_blank" href="/tournaments/?show_tournament=' . $matches[$key['game_id']]['tournament_id'] . '">' . $matches[$key['game_id']]['first_team_name'] . ' VS ' . $matches[$key['game_id']]['second_team_name'] . '</a>';
                        $classes_for_table = 'good is_on';
                    }else {
                        $current_match = $lang_Const['Duels_title_noMatches'];
                        $classes_for_table = '';
                    }

                    // if ($logged_user['uid'] == 38264) {
                    //     echo '<pre>';
                    //         var_dump($matches[$key['game_id']]['match_id']);
                    //     echo '</pre>';
                    // }
                    
                    if ($key['location'] == 'eu') {
                        echo '
                            <tr class="one_tournament_server">
                                <td style="padding-right: 15px;" class="tournament_server_id">' . $key['server_id'] . '</td>
                                <td class="tournament_server_ip">' . $key['server_ip'] . '</td>
                                <td style="padding-right: 7px;" class="tournament_server_status '.$server_status[1].'">' . $server_status[0] . '</td>
                                <td class="tournament_server_responce" id="tournament_' . $key['server_id'] . '">' . $responce_server . '</td>
                                <td class="tournament_server_current_game ' . $classes_for_table . '" style="width: 14%;">' . $current_match . '</td>
                                <td class="tournament_server_id_options manager_btn">
                                    <div class="btn make_server_free center_btn" onclick="start_server(' . $key['server_id'] . ', '."1".')">Start</div>
                                    <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['server_id'] . ', '."1".')">Turn off</div>
                                    <div class="btn make_server_busy center_btn" onclick="restart_server(' . $key['server_id'] . ', '."1".')">Restart</div>
                                    <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['server_id'] . ', '."1".')">Status</div>
                                    <div class="btn make_server_busy center_btn" style="width: 6.5rem;padding: 10px 0;" onclick="check_update_server(' . $key['server_id'] . ', '."1".')">Check Update</div>
                                    <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['server_id'] . ', '."1".')">Update</div>
                                </td>
                            </tr>
                        ';
                    }
                }
                echo '</table>';
            } else {
                echo '<div id="server-Tournament-toggler" class="manager-box error_manager center">Ошибка с получением данных серверов.</div>';
            }

            //? Danger-Zone Сервера
            if (@$DZ_Servers->num_rows > 0) {
                echo '
                    <table id="server-DangerZone-toggler" class="manager-box DangerZone table_servers">
                        <tr>
                            <td>ID</td>
                            <td>'.$lang_Const['Server_Manager_table_header_ipServer_title'].'</td>
                            <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                            <td class="tournament_server_status">'.$lang_Const['Server_Manager_table_header_responce_title'].'</td>
                            <td>'.$lang_Const['tournaments_titile_Location'].'</td>
                            <td>'.$lang_Const['Duels_title_Action'].'</td>
                        </tr>';
                                // <td>Локация</td>

                foreach ($arr['DZ'] as $key) {
                    if ($key['status'] == 0) {
                        $server_status_dz = [$lang_Const['Duels_title_Available'], 'status_Busy' ];
                    } else if ($key['status'] == 1) {
                        $server_status_dz = [$lang_Const['Duels_title_Busy'], 'status_Available'];
                    }

                    if ($key['location'] == 'ru') {
                        $location = '<img src="../img/server_locations/ru.svg" alt="ru">';
                    }
                    else if($key['location'] == 'eu'){
                        $location = '<img src="../img/server_locations/eu.svg" alt="eu">';
                    }

                    echo '
                                <tr class="one_tournament_server" id="danger-zone">
                                    <td class="tournament_server_id">' . $key['id'] . '</td>
                                    <td class="tournament_server_ip">' . $key['server_ip'] . '</td>
                                    <td class="tournament_server_status '.$server_status_dz[1].'">' . $server_status_dz[0] . '</td>
                                    <td class="tournament_server_responce" id="dangerzone_' . $key['id'] . '">' . $responce_server . '</td>
                                    <td class="tournament_server_location" style="width: 14%;">'. $location .'</td>
                                    <td class="tournament_server_id_options manager_btn">
                                        <div class="btn make_server_busy center_btn" onclick="start_server(' . $key['id'] . ', '."2".')">Start</div>
                                        <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['id'] . ', '."2".')">Turn off</div>
                                        <div class="btn make_server_busy center_btn" id="manager_command" data-mode="DangerZone" onclick="restart_server(' . $key['id'] . ')">Restart</div>
                                        <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['id'] .', '."2".')">Status</div>
                                        <div class="btn make_server_busy center_btn" style="width: 6.5rem;padding: 10px 0;" onclick="check_update_server(' . $key['id'] . ', '."2".')">Check Update</div>
                                        <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['id'] . ', '."2".')">Update</div>
                                    </td>
                                </tr>
                                ';
                                //<td class="tournament_server_current_game ' . $classes_for_table . '" style="width: 14%;">'. $key['location'] .'</td>
                }
                echo '</table>';
            } else {
                echo '<div id="server-DangerZone-toggler" class="manager-box error_manager center">Ошибка с получением данных серверов.</div>';
            }

            //? Duels Сервера
            if (@$DL_Servers->num_rows > 0) {
                echo '
                            <table id="server-Duels-toggler" class="manager-box Duels table_servers">
                                <tr>
                                    <td>ID</td>
                                    <td>'.$lang_Const['Server_Manager_table_header_ipServer_title'].'</td>
                                    <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                                    <td class="tournament_server_status">'.$lang_Const['Server_Manager_table_header_responce_title'].'</td>
                                    <td>'.$lang_Const['tournaments_titile_Location'].'</td>
                                    <td>'.$lang_Const['Duels_title_Action'].'</td>
                                </tr>';


                foreach ($arr['DL'] as $key) {
                    if ($key['status'] == 0) {
                        $server_status_dz = [$lang_Const['Duels_title_Available'], 'status_Busy' ];
                    } else if ($key['status'] == 1) {
                        $server_status_dz = [$lang_Const['Duels_title_Busy'], 'status_Available'];
                    }

                    if ($key['location'] == 'ru') {
                        $location = '<img src="../img/server_locations/ru.svg" alt="ru">';
                    }
                    else if($key['location'] == 'eu'){
                        $location = '<img src="../img/server_locations/eu.svg" alt="eu">';
                    }

                    echo '
                                <tr class="one_tournament_server">
                                    <td class="tournament_server_id">' . $key['id'] . '</td>
                                    <td class="tournament_server_ip">' . $key['server_ip'] . '</td>
                                    <td class="tournament_server_status '.$server_status_dz[1].'">' . $server_status_dz[0] . '</td>
                                    <td class="tournament_server_responce" id="Duels_' . $key['id'] . '">' . $responce_server . '</td>
                                    <td class="tournament_server_location" style="width: 14%;">'. $location .'</td>
                                    <td class="tournament_server_id_options manager_btn">
                                        <div class="btn make_server_free center_btn" onclick="start_server(' . $key['id'] . ', '."3".')">Start</div>
                                        <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['id'] . ', '."3".')">Turn off</div>
                                        <div class="btn make_server_busy center_btn" onclick="restart_server(' . $key['id'] . ', '."3".')">Restart</div>
                                        <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['id'] . ', '."3".')">Status</div>
                                        <div class="btn make_server_busy center_btn" style="width: 6.5rem;padding: 10px 0;" onclick="check_update_server(' . $key['id'] . ', '."3".')">Check Update</div>
                                        <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['id'] . ', '."3".')">Update</div>
                                    </td>
                                </tr>
                                ';
                }
                echo '</table>';
            } else {
                echo '<div id="server-Duels-toggler" class="manager-box error_manager center">Ошибка с получением данных серверов.</div>';
            }

            //? GunGame Сервера
            if (@$GG_Servers->num_rows > 0) {
                echo '
                            <table id="server-GunGame-toggler" class="manager-box GunGame table_servers">
                                <tr>
                                    <td>ID</td>
                                    <td>'.$lang_Const['Server_Manager_table_header_ipServer_title'].'</td>
                                    <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                                    <td class="tournament_server_status">'.$lang_Const['Server_Manager_table_header_responce_title'].'</td>
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
                                        <div class="btn make_server_free center_btn" onclick="start_server(' . $key['id'] . ', '."4".')">Start</div>
                                        <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['id'] . ', '."4".')">Turn off</div>
                                        <div class="btn make_server_busy center_btn" onclick="restart_server(' . $key['id'] . ', '."4".')">Restart</div>
                                        <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['id'] . ', '."4".')">Status</div>
                                        <div class="btn make_server_busy center_btn" style="width: 6.5rem;padding: 10px 0;" onclick="check_update_server(' . $key['id'] . ', '."4".')">Check Update</div>
                                        <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['id'] . ', '."4".')">Update</div>
                                    </td>
                                </tr>
                                ';
                }
                echo '</table>';
            } else {
                echo '<div id="server-GunGame-toggler" class="manager-box error_manager center">Ошибка с получением данных серверов.</div>';
            }

            //? Game Сервера
            if (@$Game_Servers->num_rows > 0) {
                echo '
                            <table id="server-GameServer-toggler" class="manager-box GameServers table_servers">
                                <tr>
                                    <td>ID</td>
                                    <td>'.$lang_Const['Server_Manager_table_header_ipServer_title'].'</td>
                                    <td>'.$lang_Const['Server_Manager_table_header_Category'].'</td>
                                    <td>'.$lang_Const['DangerZone_titile_Status'].'</td>
                                    <td class="tournament_server_status">'.$lang_Const['Server_Manager_table_header_responce_title'].'</td>
                                    <td>'.$lang_Const['Duels_title_Action'].'</td>
                                </tr>';


                foreach ($arr['GS'] as $key) {
                    if ($key['status'] == 'active') {
                        $server_status_dz = ['Включен', 'status_Busy' ];
                    } else if ($key['status'] == 1) {
                        $server_status_dz = ['Выключен', 'status_Available'];
                    }

                    echo '
                                <tr class="one_tournament_server">
                                    <td class="tournament_server_id">' . $key['id'] . '</td>
                                    <td class="tournament_server_ip">' . $key['ip'] . '</td>
                                    <td class="tournament_server_category">' . $key['name'] . '</td>
                                    <td class="tournament_server_status '.$server_status_dz[1].'">' . $server_status_dz[0] . '</td>
                                    <td class="tournament_server_responce" id="GameServer_' . $key['id'] . '">' . $responce_server . '</td>
                                    <td class="tournament_server_id_options manager_btn">
                                        <div class="btn make_server_free center_btn" onclick="start_server(' . $key['id'] . ', '."5".')">Start</div>
                                        <div class="btn make_server_busy red center_btn" onclick="stop_server(' . $key['id'] . ', '."5".')">Turn off</div>
                                        <div class="btn make_server_busy center_btn" onclick="restart_server(' . $key['id'] . ', '."5".')">Restart</div>
                                        <div class="btn make_server_busy red center_btn" onclick="status_server(' . $key['id'] . ', '."5".')">Status</div>
                                        <div class="btn make_server_busy center_btn" style="width: 6.5rem;padding: 10px 0;" onclick="check_update_server(' . $key['id'] . ', '."5".')">Check Update</div>
                                        <div class="btn make_server_busy red center_btn" onclick="update_server(' . $key['id'] . ', '."5".')">Update</div>
                                    </td>
                                </tr>
                                ';
                }
            echo '</table>';
                
            } else {
                echo '<div id="server-GameServer-toggler" class="manager-box error_manager center">Ошибка с получением данных серверов.</div>';
            }
        }
        ?>
    </div>
</div>

<script src="../jQuery.js" defer></script>
<script src="/lang.js?v=1.3" defer></script>
<script src="server_manager.js?v=1.5" defer></script>