let result;

let gamemode;

function get_mode(mode) { 
    if (mode === 1) {
        gamemode = 'tournament';
    }
    else if(mode === 2){
        gamemode = 'dangerzone';
    }
    else if(mode === 3){
        gamemode = 'Duels';
    }
    else if(mode === 4){
        gamemode = 'GunGame';
    }
    else if(mode === 5){
        gamemode = 'GameServer';
    }

    return gamemode;
 }

const loader = `
<div class="spinner-box">
  <div class="pulse-container">  
    <div class="pulse-bubble pulse-bubble-1"></div>
    <div class="pulse-bubble pulse-bubble-2"></div>
    <div class="pulse-bubble pulse-bubble-3"></div>
  </div>
</div>
`;

//? Функции управлением сервера

function start_server(server_id, mode) {
    $('#'+get_mode(mode)+'_'+server_id).html(loader);
    $.ajax({
        url: 'handler.php',
        type: 'POST',
        dataType: 'json',
        data: { request: 'start_server', server_id, gamemode},
        success: function (response) {
            console.log(response);
            result = response.match(/OK|already running/);

            if (result == 'OK')
                $('#'+gamemode+'_'+server_id).html('Сервер <span class="status_Busy">ONLINE</span>');
            else if(result == 'already running')
                $('#'+gamemode+'_'+server_id).html('Сервер уже <span class="status_Busy">ONLINE</span>');
            else{
                $('#'+gamemode+'_'+server_id).addClass('response_warn');
                $('#'+gamemode+'_'+server_id).text('Возникла непредвиденная ошибка, обратитесть к разроботчику!');
            }
        },
        error: function (response) {
            console.error(response);
            $('#'+gamemode+'_'+server_id).addClass('response_warn');
            $('#'+gamemode+'_'+server_id).text('Возникла непредвиденная ошибка, обратитесть к разроботчику!');
        }
    });
}

function stop_server(server_id, mode) {
    $('#'+get_mode(mode)+'_'+server_id).html(loader);
    $.ajax({
        url: 'handler.php',
        type: 'POST',
        dataType: 'json',
        data: { request: 'stop_server', server_id, gamemode},
        success: function (response) {
            console.log(response);
            if (response == 0)
                $('#'+gamemode+'_'+server_id).html('Сервер <span class="status_Available">OFFLINE</span>');
            else{
                $('#'+gamemode+'_'+server_id).addClass('response_warn');
                $('#'+gamemode+'_'+server_id).text('Возникла непредвиденная ошибка, обратитесть к разроботчику!');
            }
            
        },
        error: function (response) {
            console.error(response);
            $('#'+gamemode+'_'+server_id).addClass('response_warn');
            $('#'+gamemode+'_'+server_id).text('Возникла непредвиденная ошибка, обратитесть к разроботчику!');
        }
    });
}

function restart_server(server_id, mode) {
    $('#'+get_mode(mode)+'_'+server_id).html(loader);
    $.ajax({
        url: 'handler.php',
        type: 'POST',
        dataType: 'json',
        data: { request: 'restart_server', server_id, gamemode},
        success: function (response) {
            console.log(response);
            $('#'+gamemode+'_'+server_id).addClass('response_complete');
            $('#'+gamemode+'_'+server_id).text('Сервер перезапустился');
        },
        error: function (response) {
            console.error(response);
            $('#'+gamemode+'_'+server_id).addClass('response_warn');
            $('#'+gamemode+'_'+server_id).text('Возникла непредвиденная ошибка, обратитесть к разроботчику!');
        }
    });
}

function status_server(server_id, mode) {
    $('#'+get_mode(mode)+'_'+server_id).html(loader);
    $.ajax({
        url: 'handler.php',
        type: 'POST',
        dataType: 'json',
        data: { request: 'status_server', server_id, gamemode},
        success: function (response) {
            console.log(response);
            result = response.match(/ONLINE|OFFLINE|STARTED|STOPPED/);
            if (result == 'STARTED') 
                result[0] = 'ONLINE';
            else if(result == 'STOPPED')
                result[0] = 'OFFLINE';

            if (result[0] == 'ONLINE')
                result[1] = 'status_Busy';
            else if (result == 'OFFLINE')
                result[1] = 'status_Available';
            $('#'+gamemode+'_'+server_id).removeClass('response_complete');
            $('#'+gamemode+'_'+server_id).removeClass('response_info');
            $('#'+gamemode+'_'+server_id).removeClass('response_warn');
            $('#'+gamemode+'_'+server_id).html(`Статус сервера: <span class="${result[1]}">${result[0]}</span>`);
        },
        error: function (response) {
            console.error(response);
            $('#'+gamemode+'_'+server_id).addClass('response_warn');
            $('#'+gamemode+'_'+server_id).text('Возникла непредвиденная ошибка, обратитесть к разроботчику!');
        }
    });
}

function update_server(server_id, mode) {
    $('#'+get_mode(mode)+'_'+server_id).html(loader);
    $.ajax({
        url: 'handler.php',
        type: 'POST',
        dataType: 'json',
        data: { request: 'update_server', server_id, gamemode},
        success: function (response) {
            console.log(response);
            let local_build = response.match(/(Local build): \D+[0-9]{2}m([0-9]*)/)[2];
            let remote_build = response.match(/(Remote build:) \D+[0-9]{2}m([0-9]*)/)[2];
            let complete = response.match(/\D+[0-9]{2}m(Complete)/);

            if (local_build == remote_build) {
                $('#'+gamemode+'_'+server_id).addClass('response_info');
                $('#'+gamemode+'_'+server_id).text('В данный момент сервер уже обновлён!');
            }

            if (complete) {
                if (complete[1] == 'Complete') {
                    $('#'+gamemode+'_'+server_id).addClass('response_complete');
                    $('#'+gamemode+'_'+server_id).text('Обновление сервера прошло успешно!');
                }
            }
            
        },
        error: function (response) {
            console.error(response);
            $('#'+gamemode+'_'+server_id).addClass('response_warn');
            $('#'+gamemode+'_'+server_id).text('Возникла непредвиденная ошибка, обратитесть к разроботчику!');
        }
    });
}

function check_update_server(server_id, mode) {
    $('#'+get_mode(mode)+'_'+server_id).html(loader);
    $.ajax({
        url: 'handler.php',
        type: 'POST',
        dataType: 'json',
        data: { request: 'check_update_server', server_id, gamemode},
        success: function (response) {
            //  let local_build = response.match(/(Local build:) \D+32m([0-9]*)/)[2];
            console.log(response);
             let local_build = response.match(/(Local build): \D+[0-9]{2}m([0-9]*)/)[2];
             let remote_build = response.match(/(Remote build:) \D+[0-9]{2}m([0-9]*)/)[2];
            if (local_build !== remote_build) {
                $('#'+gamemode+'_'+server_id).addClass('response_complete');
                $('#'+gamemode+'_'+server_id).text('У вас есть обновление, нажмите на кнопку "Обновить" чтобы обновить сервер');
            }
            else{
                $('#'+gamemode+'_'+server_id).addClass('response_info');
                $('#'+gamemode+'_'+server_id).text('В данный момент сервер уже обновлён!');
            }
        },
        error: function (response) {
            console.error(response);
            $('#'+gamemode+'_'+server_id).addClass('response_warn');
            $('#'+gamemode+'_'+server_id).text('Возникла непредвиденная ошибка, обратитесть к разроботчику!');
        }
    });
}

var gets = (function() {
    var url = window.location.search;
    var b = new Object();
    url = url.substring(1).split("&");
    for (var i = 0; i < url.length; i++) {
      c = url[i].split("=");
        if(typeof(c[1]) != 'undefined') {
            b[c[0]] = c[1];
        } else {
            b[c[0]] = null;
        }
    }
    return b;
})();

//? Отвечает за переключением между категориями

if(typeof(gets['GunGame']) != 'undefined') {
    $('#server-GunGame-toggler').attr('checked', true);
} else if(typeof(gets['DangerZone']) != 'undefined') {
    $('#server-DangerZone-toggler').attr('checked', true);
} else if(typeof(gets['Duels']) != 'undefined') {
    $('#server-Duels-toggler').attr('checked', true);
}else if(typeof(gets['GameServer']) != 'undefined') {
    $('#server-GameServers-toggler').attr('checked', true);
}
else {
    $('#server-Tournament-toggler').attr('checked', true);
}

//? Добавление в адресную строку параметры

// $('label[for="server-Tournament-toggler"]').click(function(){
//     history.pushState(null, null, '/server_manager/');
// });
// $('label[for="server-DangerZone-toggler"]').click(function(){
//     history.pushState(null, null, '/server_manager/?DangerZone');
// });
// $('label[for="server-Duels-toggler"]').click(function(){
//     history.pushState(null, null, '/server_manager/?Duels');
// });
// $('label[for="server-GunGame-toggler"]').click(function(){
//     history.pushState(null, null, '/server_manager/?GunGame');
// });
// $('label[for="server-GameServers-toggler"]').click(function(){
//     history.pushState(null, null, '/server_manager/?GameServer');
// });
