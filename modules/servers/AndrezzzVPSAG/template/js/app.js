/**
 *	WHMCS Server Provisioning version 1.1
 *
 *	@package     WHMCS
 *	@copyright   Andrezzz
 *	@link        https://www.andrezzz.pt
 *	@author      André Antunes <andreantunes@andrezzz.pt>
 */

function $_(id) {
    return document.getElementById(id);
}

function AndrezzzVPS_API(action, alert = true, json = {}) {
    AndrezzzVPS_Loading(true);
    
    $.post(productURL + '&modop=custom&a=ClientAreaAPI&api=' + action, json,
        function(data) {
            if (data.result === 'success') {
                switch (action) {
                    case 'IPv6':
                        $_('ipv6').parentElement.innerHTML = data.data;
                        data.data = 'IPv6 created: ' + data.data;
                        
                        break;

                    case 'Graphs':
                        $_('cpu-graph').innerHTML = data.cpu_img;
                        $_('cpu-graph').classList.add('graphs');

                        $_('ram-graph').innerHTML = data.mem_img;
                        $_('ram-graph').classList.add('graphs');

                        $_('disk-graph').innerHTML = data.disk_img;
                        $_('disk-graph').classList.add('graphs');
                        
                        $_('network-graph').innerHTML = data.net_img;
                        $_('network-graph').classList.add('graphs');
                        
                        break;

                    case 'Reinstall':
                        window.location.reload();
                        break;

                    case 'List backups':
                        const backupTable = $_('backupTable').getElementsByTagName('tbody')[0];
                        
                        delete data.result;
                        $('#backupTable tbody').find('tr').remove();

                        for (let i = 0; i < Object.keys(data).length; i++) {
                            const backup = data[Object.keys(data)[i]];
                            
                            const row = backupTable.insertRow();
                            const date = row.insertCell(0);
                            const size = row.insertCell(1);
                            const type = row.insertCell(2);
                            const status = row.insertCell(3);
                            const actions = row.insertCell(4);
                            
                            date.innerHTML = new Date(backup.date).toLocaleString();
                            size.innerHTML = (backup.size !== '' ? backup.size : '0.00GB');
                            type.innerHTML = backup.type;

                            if (backup.status === 'ok') {
                                status.innerHTML = '<div class="badge bg-success">Completed</div>';
                                actions.innerHTML = '<a href="#" onclick="AndrezzzVPS_API(\'Restore backup\', true, { file: ' + backup.file + ' });return false;"><i class="fas fa-history restore-icon text-secondary mr-2"></i></a> <a href="#" onclick="AndrezzzVPS_API(\'Delete backup\', true, { file: ' + backup.file + ' });return false;"><i class="fas fa-1x fa-times delete" aria-hidden="true"></i></a>';
                            } else if (backup.status === 'preparing') {
                                status.innerHTML = '<div class="badge bg-info">Preparing</div>';
                            } else if (backup.status === 'creating') {
                                status.innerHTML = '<div class="badge bg-warning">Creating ' + backup.percentage + '%</div>';
                            } else {
                                status.innerHTML = '<div class="badge bg-danger">' + backup.status + '</div>';
                            }
                        }

                        break;

                    case 'Create backup':
                        AndrezzzVPS_API('List backups', false);
                        break;

                    case 'Delete backup':
                        AndrezzzVPS_API('List backups', false);
                        break;
                        
                    case 'Get Firewall rules':
                        const firewallTable = $_('firewallTable').getElementsByTagName('tbody')[0];
                        
                        delete data.result;
                        $('#firewallTable tbody').find('tr:not(:last)').remove();

                        for (let i = 0; i < Object.keys(data).length; i++) {
                            const rule = data[Object.keys(data)[i]];
                            
                            const row = firewallTable.insertRow(1);
                            const action = row.insertCell(0);
                            const port = row.insertCell(1);
                            const protocol = row.insertCell(2);
                            const source = row.insertCell(3);
                            const note = row.insertCell(4);
                            const actions = row.insertCell(5);

                            action.innerHTML = rule.action;
                            port.innerHTML = rule.port;
                            protocol.innerHTML = rule.protocol;
                            source.innerHTML = rule.source;
                            note.innerHTML = rule.note;
                            actions.innerHTML = '<a href="#" onclick="AndrezzzVPS_API(\'Delete Firewall rule\', true, { rule_id: ' + rule.id + ' });return false;"><i class="fas fa-1x fa-times delete" aria-hidden="true"></i></a>';
                        }

                        $('#firewallTable tbody').append($('#firewallTable tbody tr:first'));

                        break;

                    case 'Add Firewall rules':
                        AndrezzzVPS_API('Get Firewall rules', false);
                        break;

                    case 'Delete Firewall rule':
                        AndrezzzVPS_API('Get Firewall rules', false);
                        break;

                    case 'Commit Firewall rules':
                        AndrezzzVPS_API('Get Firewall rules', false);
                        break;

                    case 'ISO Images':
                        const isoSelect = $_('isoID');

                        $('#isoID').empty();

                        for (let i = 0; i < data.iso.length; i++) {
                            const iso = data.iso[i];
                            const option = document.createElement('option');
                            
                            option.value = iso.id;
                            option.innerHTML = iso.iso_image;
                            option.selected = (data.current_iso !== 0 && data.current_iso == iso.id);

                            isoSelect.appendChild(option);
                        }
                }
                
                AndrezzzVPS_Loading(false);
                
                if (alert) {
                    AndrezzzVPS_Alert('success', (typeof data.data === 'string' ? data.data : lang.moduleactionsuccess));
                }
            } else {
                AndrezzzVPS_Loading(false);
                AndrezzzVPS_Alert('error', (typeof data.message === 'string' ? data.message : lang.moduleactionfailed));
            }
        }
    );
}

function AndrezzzVPS_VNC() {
    window.open(productURL + '&modop=custom&a=VNC', '_blank', 'toolbar=0,location=0,menubar=0');
}

function AndrezzzVPS_ShowTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y + 20,
        left: x - 20,
        border: '1px solid #CCCCCC',
        padding: '2px',
        'background-color': '#EFEFEF',
        'z-index': 10000,
        opacity: 0.80
    }).appendTo('body').fadeIn(200);
}

function AndrezzzVPS_MakeData(data) {
    var now = new Date().getTime();
    var updateInterval = 1000;

    i = 0;
    var fdata = [];

    for (x in data) {
        fdata.push([now += updateInterval, data[x]]);
        i++;
    }

    return fdata;
}

function AndrezzzVPS_Loading(status) {
    $_('loading').style.left = ((document.body.clientWidth - $('#loading').width()) / 2).toString() + 'px';

    if (status) {
        $('#loading').show();
    } else {
        $('#loading').hide();
    }
}

function AndrezzzVPS_LiveResourceGraph(id, data, options, showIn, showTime) {
    $.plot($('#' + id), data, options);

    if (!('tooltip' in options)) {
        var previousPoint = null;

        $('#' + id).bind('plothover', function(event, pos, item) {
            $('#x').text(pos.x.toFixed(2));
            $('#y').text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    $('#tooltip').remove();

                    var x = item.datapoint[0].toFixed(2);
                    var y = item.datapoint[1].toFixed(2);
                    var time = '';

                    if (showTime) {
                        time = nDate(x, 'm/d H:i:s');
                    }

                    if (id === 'ntw_plot' || id === 'io_read_plot' || id === 'io_write_plot') {
                        var yval = parseInt(y);
                        var show_ntw_in;

                        if (yval <= 1024) {
                            show_ntw_in = 'B/s';
                        } else if (yval > 1024 && yval <= (1024 * 1024)) {
                            yval = (yval / 1024).toFixed(2);
                            show_ntw_in = 'KB/s';
                        } else if (yval > (1024 * 1024) && yval <= (1024 * 1024 * 1024)) {
                            yval = (yval / 1024 / 1024).toFixed(2);
                            show_ntw_in = 'MB/s';
                        } else if (yval > (1024 * 1024 * 1024)) {
                            yval = (yval / 1024 / 1024 / 1024).toFixed(2);
                            show_ntw_in = 'GB/s';
                        }

                        AndrezzzVPS_ShowTooltip(item.pageX, item.pageY, item.series.label + ' ' + yval + ' ' + show_ntw_in + '&nbsp; at &nbsp;' + time);
                    } else {
                        AndrezzzVPS_ShowTooltip(item.pageX, item.pageY, parseFloat(y) + ' ' + showIn + time);
                    }

                }
            } else {
                $('#tooltip').remove();
                previousPoint = null;
            }
        });
    }
}

$(document).ready(function () {
    $('#pills-tab').scrollingTabs({
        enableSwiping: true,
        bootstrapVersion: 5,
        cssClassLeftArrow: 'fa fa-arrow-left',
        cssClassRightArrow: 'fa fa-arrow-right'
    });

    var cpuOptions = {
        series: {
            lines: {
                show: true,
                lineWidth: 0.1,
                fill: true
            }
        },
        xaxis: {
            show: true,
            color: 'white',
            mode: 'time',
            tickSize: [1, 'second'],
            tickFormatter: function(v, axis) {
                var date = new Date(v);

                if (date.getSeconds() % 5 == 0) {
                    var hours = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
                    var minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
                    var seconds = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();

                    return hours + ':' + minutes + ':' + seconds;
                } else {
                    return '';
                }
            },
            axisLabel: ' ',
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 10
        },
        yaxis: {
            show: false
        },
        grid: {
            borderWidth: 0,
            borderColor: '#fff',
            hoverable: true,
        },
    };

    var cpudata = [];
    var totalPoints = 30;

    for (var i = 0; i < totalPoints; ++i) {
        cpudata.push(0.1);
    }

    var cpuDataset = [
        { label: '', data: AndrezzzVPS_MakeData(cpudata), color: '#e51e88' }
    ];

    var timerServerLoads;

    $_('AndrezzzVPS').style.display = 'block';
    AndrezzzVPS_LiveResourceGraph('cpuHistory', cpuDataset, cpuOptions, '%', false);

    function AndrezzzVPS_UpdateStatitics() {
        if ($_('overview-tab').ariaSelected !== 'true') return timerServerLoads = setTimeout(AndrezzzVPS_UpdateStatitics, 10000);

        clearTimeout(timerServerLoads);
        
        if (timerServerLoads) {
            $.get(productURL + '&modop=custom&a=ClientAreaAPI&api=Server%20Info',
                function(serverInfo) {
                    if (serverInfo.result === 'success') {
                        serverInfo.ram_usage = serverInfo.ram_usage / (1000 * 1000 * 1000);
                        serverInfo.ram_usage_view = serverInfo.ram_usage.toFixed(2);
                        serverInfo.ram_percentage = ((serverInfo.ram_usage * 100) / serverInfo.ram).toFixed();
                        serverInfo.bandwidth_percentage = ((serverInfo.bandwidth_usage * 100) / (serverInfo.bandwidth * 1000)).toFixed();

                        $('#ramPercentBar').css('background', (serverInfo.ram_percentage <= 40 ? '#06d79c' : serverInfo.ram_percentage < 80 ? 'orange' :  serverInfo.ram_percentage >= 80 ? 'red' : '#ff0000'));
                        $('#ramPercentBar').css('width', serverInfo.ram_percentage + '%');
                        $('#ramPercentBar').html(serverInfo.ram_percentage + ' %');
                        $('#ramPercentBar').attr('tooltip', serverInfo.ram_percentage + ' %');
                        $('#ramPercentVal').html(serverInfo.ram_usage_view + ' / ' + serverInfo.ram + ' GB');

                        $('#bandwidthPercentBar').css('background', (serverInfo.bandwidth_percentage <= 40 ? '#9c06d7' : serverInfo.bandwidth_percentage < 80 ? 'orange' :  serverInfo.bandwidth_percentage >= 80 ? 'red' : '#ff0000'));
                        $('#bandwidthPercentBar').css('width', serverInfo.bandwidth_percentage + '%');
                        $('#bandwidthPercentBar').html(serverInfo.bandwidth_percentage + ' %');
                        $('#bandwidthPercentBar').attr('tooltip', serverInfo.bandwidth_percentage + ' %');
                        $('#bandwidthPercentVal').html(serverInfo.bandwidth_usage + ' / ' + serverInfo.bandwidth * 1000 + ' GB');

                        serverInfo.cpu_usage = (serverInfo.cpu_usage * 100).toFixed(0);
                        
                        cpudata.shift();
                        cpudata.push(parseFloat(serverInfo.cpu_usage));

                        cpuDataset = [
                            { label: '', data: AndrezzzVPS_MakeData(cpudata), color: '#3498DB' }
                        ];

                        AndrezzzVPS_LiveResourceGraph('cpuHistory', cpuDataset, cpuOptions, '%', false);

                        $('.used-cpu').html(serverInfo.cpu_usage + '%');
                    } else {
                        AndrezzzVPS_Alert('error', (typeof serverInfo.message === 'string' ? serverInfo.message : lang.moduleactionfailed));
                    }
                }
            );
        } else {
            const serverInfo = serverInfoInitial;

            serverInfo.ram_usage = serverInfo.ram_usage / (1000 * 1000 * 1000);
            serverInfo.ram_usage_view = serverInfo.ram_usage.toFixed(2);
            serverInfo.ram_percentage = ((serverInfo.ram_usage * 100) / serverInfo.ram).toFixed();
            serverInfo.bandwidth_percentage = ((serverInfo.bandwidth_usage * 100) / (serverInfo.bandwidth * 1000)).toFixed();

            $('#ramPercentBar').css('background', (serverInfo.ram_percentage <= 40 ? '#06d79c' : serverInfo.ram_percentage < 80 ? 'orange' :  serverInfo.ram_percentage >= 80 ? 'red' : '#ff0000'));
            $('#ramPercentBar').css('width', serverInfo.ram_percentage + '%');
            $('#ramPercentBar').html(serverInfo.ram_percentage + ' %');
            $('#ramPercentBar').attr('tooltip', serverInfo.ram_percentage + ' %');
            $('#ramPercentVal').html(serverInfo.ram_usage_view + ' / ' + serverInfo.ram + ' GB');

            $('#bandwidthPercentBar').css('background', (serverInfo.bandwidth_percentage <= 40 ? '#9c06d7' : serverInfo.bandwidth_percentage < 80 ? 'orange' :  serverInfo.bandwidth_percentage >= 80 ? 'red' : '#ff0000'));
            $('#bandwidthPercentBar').css('width', serverInfo.bandwidth_percentage + '%');
            $('#bandwidthPercentBar').html(serverInfo.bandwidth_percentage + ' %');
            $('#bandwidthPercentBar').attr('tooltip', serverInfo.bandwidth_percentage + ' %');
            $('#bandwidthPercentVal').html(serverInfo.bandwidth_usage + ' / ' + serverInfo.bandwidth * 1000 + ' GB');

            serverInfo.cpu_usage = (serverInfo.cpu_usage * 100).toFixed(0);

            cpudata.shift();
            cpudata.push(parseFloat(serverInfo.cpu_usage));

            cpuDataset = [
                { label: '', data: AndrezzzVPS_MakeData(cpudata), color: '#3498DB' }
            ];

            AndrezzzVPS_LiveResourceGraph('cpuHistory', cpuDataset, cpuOptions, '%', false);

            $('.used-cpu').html(serverInfo.cpu_usage + '%');
        }

        timerServerLoads = setTimeout(AndrezzzVPS_UpdateStatitics, 10000);
    }

    AndrezzzVPS_UpdateStatitics();
});

function AndrezzzVPS_ChooseOS(button) {
    var newOS = $_('newOS').value;

    if (newOS !== '0') {
        newOS = $('[data-os="' + newOS + '"]')[0];

        newOS.classList.remove('SelectedOS');
        $_(newOS.dataset.group + '-os').classList.remove('selected');
        $_(newOS.dataset.group + '-version').innerText = 'SELECT VERSION';
    }

    $_('newOS').value = button.dataset.os;

    button.classList.add('SelectedOS');
    $_(button.dataset.group + '-os').classList.add('selected');
    $_(button.dataset.group + '-version').innerText = button.innerText;
}

function AndrezzzVPS_ShowPassword() {
    const vpsPassword = $_('vpsPassword');
    const showPasswordIcon = $_('showPasswordIcon');

    if (vpsPassword.type === 'password') {
        vpsPassword.type = 'text';
        showPasswordIcon.classList = 'fa-solid fa-eye-slash';
    } else {
        vpsPassword.type = 'password';
        showPasswordIcon.classList = 'fa-solid fa-eye';
    }
}

function AndrezzzVPS_Alert(type, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
      
    Toast.fire({
        icon: type,
        title: message
    });
}