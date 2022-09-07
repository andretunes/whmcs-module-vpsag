{**
 *	VPSAG WHMCS Server Provisioning version 1.1
 *
 *	@package     WHMCS
 *	@copyright   Andrezzz
 *	@link        https://www.andrezzz.pt
 *	@author      André Antunes <andreantunes@andrezzz.pt>
 *}

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="author" content="Andrezzz">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Andrezzz - VPS Panel</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.0/css/all.min.css" rel="stylesheet" integrity="sha256-AbA177XfpSnFEvgpYu1jMygiLabzPCJCRIBtR5jGc0k=" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/jquery-bootstrap-scrolling-tabs@2.6.1/dist/jquery.scrolling-tabs.min.css" rel="stylesheet" integrity="sha256-nxJQ/J+p10Kz29Kd5up0e3Eem6RfnnfRRH/gJh5lMVc=" crossorigin="anonymous">
        <link href="{$WEB_ROOT}/clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=DeliverFile&file=app.min.css" rel="stylesheet">

        <script type="text/javascript">
            var productURL = '{$WEB_ROOT}/clientarea.php?action=productdetails&id={$serviceid}';
            var serverInfoInitial = JSON.parse('{$serverInfo|@json_encode}');
            var lang = {
                moduleactionfailed: '{$LANG.moduleactionfailed}!',
                moduleactionsuccess: '{$LANG.moduleactionsuccess}'
            };
        </script>
    </head>
    <body class="text-center">
        <div id="loading" class="fw-bold" style="display: none;">
            <span class="spinner-border spinner-border-sm" style="width: 3rem; height: 3rem;" id="loading-spinner" role="status" aria-hidden="true"></span>
        </div>

        <div id="AndrezzzVPS" style="display: none;">
            <div class="title-block text-center dashboard-title mb-3">VPS Information</div>

            <div class="row mb-3">
                <div class="col-lg-6 col-sm-6 col-md-12 mb-3 text-center">
                    <div class="border p-2">
                        <div class="mb-2">
                            <span><img src="{$serverInfo['operatingSystem']['image']}" width="64px" height="64px" alt="{$serverInfo['operatingSystem']['name']}"/></span>
                        </div>
                        <div class="information">
                            <span class="form-label dashboard-value d-inline-block mb-2">{$serverInfo['operatingSystem']['name']}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-12 mb-3 text-center">
                    <div class="border p-2">
                        <div class="mb-2">
                            <span><img src="{$serverInfo['statusImage']}" height="64px"/></span>
                        </div>
                        <div class="information">
                            <span class="form-label dashboard-value d-inline-block mb-2">{$serverInfo['statusDescription']}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-sm-12 col-md-12 mb-3">
                    <div class="border p-2">
                        <div class="row w-100">
                            <div class="col-4 without-mb text-center" tooltip="{if $serverInfo['status'] !== 'running'}Start the VPS{else}Stop the VPS{/if}" data-original-title="" title="">
                                <a onclick="AndrezzzVPS_API('{if $serverInfo['status'] !== 'running'}Start{else}Stop{/if}');return false;"><i class="fas fa-{if $serverInfo['status'] !== 'running'}play start{else}stop stop{/if} mr-2" aria-hidden="true"></i></a>
                            </div>
                            <div class="col-4 without-mb text-center" tooltip="Reboot the VPS" data-original-title="" title="">
                                <a onclick="AndrezzzVPS_API('Reboot');return false;"><i class="fas fa-sync reboot mr-2" aria-hidden="true"></i></a>
                            </div>
                            <div class="col-4 without-mb text-center" tooltip="Connect to the VPS via VNC" data-original-title="" title="">
                                <a onclick="AndrezzzVPS_VNC();"><img src="{$images['vnc']}" class="vnc"/></a>
                            </div>
                            <div class="col-md-12" style="margin-top: 5px;">
                                <label class="form-label d-inline-block;">Uptime:</label>
                                <span class="form-label dashboard-value d-inline-block mr-2">{$serverInfo['uptime_text']}</span>
                                <br />
                                <label class="form-label d-inline-block;">IPv4:</label>
                                <span class="form-label dashboard-value d-inline-block mr-2">{$serverInfo['ip']}</span>
                                <br />
                                <label class="form-label d-inline-block;">IPv6:</label>
                                <span class="form-label dashboard-value d-inline-block mr-2">{$serverInfo['ipv6']}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-tab" id="dashboard">
                <ul class="nav nav-tabs mb-4 dash-tabs" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" role="tab" aria-controls="overview" aria-selected="true"><i class="fa-solid fa-signal"></i> Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="graphs-tab" data-bs-toggle="tab" data-bs-target="#graphs" onclick="AndrezzzVPS_API('Graphs', false, { time: 'hour' });return false;" role="tab" aria-controls="graphs" aria-selected="false"><i class="fa-solid fa-chart-simple"></i> Graphs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="backups-tab" data-bs-toggle="tab" data-bs-target="#backups" onclick="AndrezzzVPS_API('List backups', false);return false;" role="tab" aria-controls="backups" aria-selected="false"><i class="fa-solid fa-box-archive"></i> Backups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" role="tab" aria-controls="settings" aria-selected="false"><i class="fa-solid fa-gear"></i> Settings</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <div class="head">
                            <img src="{$images['eye']}">
                            <span class="h3">Overview</span>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-6 col-sm-6 col-md-12 mb-3">
                                <div class="usage-details px-3 py-4">
                                    <p class="overview-label">RAM Usage</p>
                                    <div class="progress disk-bar">
                                        <div id="ramPercentBar" aria-valuemin="0" aria-valuemax="100" role="progressbar" class="progress-bar prog-organge" data-placement="right" tooltip="0% Used" style="background: #06d79c; width: 0%">0%</div>
                                    </div>
                                    <span id="ramPercentVal" class="used_disk_percent mr-1">0 / 0 GB</span>
                                    <span class="overview-label">Used</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-md-12 mb-3">
                                <div class="usage-details px-3 py-4">
                                    <p class="overview-label">Bandwidth Usage</p>
                                    <div class="progress disk-bar">
                                        <div id="bandwidthPercentBar" aria-valuemin="0" aria-valuemax="100" role="progressbar" class="progress-bar prog-organge" data-placement="right" tooltip="0% Used" style="background: #9c06d7; width: 0%">0%</div>
                                    </div>
                                    <span id="bandwidthPercentVal" class="used_disk_percent mr-1">0 / 0 TB</span>
                                    <span class="overview-label">Used</span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-md-12 mb-3">
                                <div class="usage-details px-3 py-2">
                                    <div class="row">
                                        <div class="col-lg-3 col-sm-3 col-md-12 py-3">
                                            <p class="overview-label">CPU Usage</p>
                                            <p class="overview-label"><span class="used-cpu">{$serverInfo['cpu_usage']}%</span></p>
                                        </div>
                                        <div class="col-lg-9 col-sm-9 col-md-12 overflow-hidden">
                                            <div id="cpuHistory" class="w-100 display" style="width: 100%; height: 150px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="graphs" role="tabpanel" aria-labelledby="graphs-tab">
                        <div class="head">
                            <img src="{$images['search']}">
                            <span class="h3">Graphs</span>
                        </div>

                        <div class="panel">
                            <div class="text-center">
                                <ul class="nav nav-pills mb-3 d-inline-flex" id="graphs-tab" role="tablist">
                                    <li class="nav-item mx-2">
                                        <a class="nav-link active" id="cpu-tab" data-bs-toggle="tab" data-bs-target="#cpu" role="tab" aria-controls="cpu" aria-selected="true">CPU</a>
                                    </li>
                                    <li class="nav-item mx-2">
                                        <a class="nav-link" id="ram-tab" data-bs-toggle="tab" data-bs-target="#ram" role="tab" aria-controls="ram" aria-selected="false" style="display: block;">RAM</a>
                                    </li>
                                    <li class="nav-item mx-2">
                                        <a class="nav-link" id="disk-tab" data-bs-toggle="tab" data-bs-target="#disk" role="tab" aria-controls="disk" aria-selected="false" style="display: block;">Disk</a>
                                    </li>
                                    <li class="nav-item mx-2">
                                        <a class="nav-link" id="network-tab" data-bs-toggle="tab" data-bs-target="#network" role="tab" aria-controls="network" aria-selected="false" style="display: block;">Network</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="cpu" role="tabpanel" aria-labelledby="cpu-tab">
                                    <span id="cpu-graph"><img src="{$images['loading']}"/></span>
                                </div>
                                <div class="tab-pane fade" id="ram" role="tabpanel" aria-labelledby="ram-tab">
                                    <span id="ram-graph"><img src="{$images['loading']}"/></span>
                                </div>
                                <div class="tab-pane fade" id="disk" role="tabpanel" aria-labelledby="disk-tab">
                                    <span id="disk-graph"><img src="{$images['loading']}"/></span>
                                </div>
                                <div class="tab-pane fade" id="network" role="tabpanel" aria-labelledby="network-tab">
                                    <span id="network-graph"><img src="{$images['loading']}"/></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="backups" role="tabpanel" aria-labelledby="backups-tab">
                        <div class="head">
                            <img src="{$images['cloud']}">
                            <span class="h3">Backups</span>
                        </div>

                        <div class="panel">
                            <div class="grey-txt mb-3">The dates for which backups of this VPS are available are listed below. You can restore or delete them accordingly.</div>

                            <div class="table-responsive">
                                <table id="backupTable" cellpadding="0" cellspacing="0" border="0" class="table table-hover tablesorter" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Size</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th width="50">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <button onclick="AndrezzzVPS_API('Create backup');return false;" class="submit-btn">Backup Now</button>

                            <br/>
                            <br/>
                            
                            <div class="grey-txt mb-3">
                                * Please keep in mind that the new backups will replace the older ones.<br/>
                                ** The automated backups will also replace your manual backups unless the automated backups are disabled.<br/>
                                *** The automated backups are made 2 times a week and are part of our disaster recovery plan. If you disable the automated backups, you also disable any chance of recovery in case of a disaster.<br/>
                                **** The backup's file system might not be fully consistent if the VPS was writing to the filesystem at the moment of the backup. For fully consistent backups, the server must be stopped while the backup is being created.
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                        <div class="row vertical-tabs">
                            <div class="col-12 col-sm-2 col-md-3 col-lg-2 v-tabs-container">
                                <div class="nav flex-md-column mx-auto left-side-tabs mb-4 mb-md-0" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link mr-2 mr-md-0 active" id="hostname-tab" data-bs-toggle="tab" data-bs-target="#hostname" role="tab" aria-controls="hostname" aria-selected="true" style="display: block;">Hostname</a>
                                    <a class="nav-link mr-2 mr-md-0" id="iso-tab" data-bs-toggle="tab" data-bs-target="#iso" onclick="AndrezzzVPS_API('ISO Images', false);" role="tab" aria-controls="iso" aria-selected="true" style="display: block;">ISO</a>
                                    <a class="nav-link mr-2 mr-md-0" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" role="tab" aria-controls="password" aria-selected="false" style="display: block;">Password</a>
                                    <a class="nav-link mr-2 mr-md-0" id="reinstall-tab" data-bs-toggle="tab" data-bs-target="#reinstall" role="tab" aria-controls="reinstall" aria-selected="false" style="display: block;">Reinstall</a>
                                    <a class="nav-link mr-2 mr-md-0" id="firewall-tab" data-bs-toggle="tab" data-bs-target="#firewall" onclick="AndrezzzVPS_API('Get Firewall rules', false);" role="tab" aria-controls="firewall" aria-selected="false" style="display: block;">Firewall</a>
                                </div>
                            </div>
                            <div class="col-12 col-sm-10 col-md-9 col-lg-10">
                                <div class="tab-content vertical-tab-content">
                                    <div class="tab-pane fade active show" id="hostname" role="tabpanel" aria-labelledby="hostname-tab">
                                        <div class="head">
                                            <img src="{$images['search']}">
                                            <span class="h3">Hostname</span>
                                        </div>

                                        <span class="badge bg-info mb-3">
                                            Sets the hostname and the rDNS. Please create the A record first.
                                        </span>
                                        
                                        <center>
                                        <div class="mb-3" style="width: 50%;">
                                            <label class="form-label d-inline-block;">Hostname:</label>
                                            <input class="form-control" id="hostnameRDNS" type="text" size="30" maxlength="128" value="{$serverInfo['hostname']}">
                                        </div>
                                        </center>

                                        <button onclick="AndrezzzVPS_API('Hostname rDNS', true, { hostname: $_('hostnameRDNS').value });;return false;" class="submit-btn">Submit</button>
                                    </div>
                                    <div class="tab-pane fade" id="iso" role="tabpanel" aria-labelledby="iso-tab">
                                        <div class="head">
                                            <img src="{$images['edit']}">
                                            <span class="h3">ISO</span>
                                        </div>

                                        <span class="badge bg-info mb-3">
                                            If you install the operating system via the ISO image, you must also configure the network interface statically. There is no DHCP server running.
                                        </span>
                                        
                                        <center>
                                        <div class="mb-3" style="width: 50%;">
                                            <label class="form-label d-inline-block;">ISO Image:</label>
                                            <select class="form-control" id="isoID"></select>
                                        </div>
                                        </center>

                                        <button onclick="AndrezzzVPS_API('Load ISO', true, { iso_id: $_('isoID').value });;return false;" class="submit-btn">Load ISO</button>

                                        {if $serverInfo['iso'] !== ''}
                                            <button onclick="AndrezzzVPS_API('Eject ISO', true);return false;" class="danger-btn">Eject ISO</button>
                                        {/if}
                                    </div>
                                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                        <div class="head">
                                            <img src="{$images['lock']}">
                                            <span class="h3">Password</span>
                                        </div>

                                        <span class="badge bg-warning mb-3">
                                            The installation password is removed from our systems after 72 hours. It is mandatory for you to change the password on your first login!
                                        </span>
                                        
                                        <center>
                                        <div class="mb-3" style="width: 50%;">
                                            <label class="form-label d-inline-block;">Password:</label>
                                            
                                            <span class="input-group mb-3">
                                                <input class="form-control" id="vpsPassword" type="password" size="30" maxlength="128" disabled value="{if $serverInfo['install_root'] != ''}{$serverInfo['install_root']}{else}Expired{/if}">
                                                <a class="input-group-text showPassword" onclick="AndrezzzVPS_ShowPassword();return false;">
                                                    <i class="fa-solid fa-eye" id="showPasswordIcon" aria-hidden="true" style="cursor: pointer"></i>
                                                </a>
                                            </span>
                                        </div>
                                        </center>

                                        <button onclick="AndrezzzVPS_API('Reset root');return false;" class="submit-btn">Reset Password</button>
                                    </div>
                                    <div class="tab-pane fade" id="reinstall" role="tabpanel" aria-labelledby="reinstall-tab">
                                        <div class="head">
                                            <img src="{$images['installing']}">
                                            <span class="h3">Reinstall</span>
                                        </div>

                                        <div class="badge bg-danger mb-3">Please understand that by reinstalling, all the data will be wiped from the server.</div>

                                        <div id="reinstallIntructions" class="col-lg-10 col-12 mx-auto">
                                            <label class="form-label">Select OS:</label>
                                            
                                            <div id="os_list" class="row mb-4">
                                                {foreach from=$operatingSystems key=$group item=$operatingSystemsGroup}
                                                <div class="col-12 col-sm-6 col-md-4 mb-2">
                                                    <div id="{$group}-os" class="os_badge media dropdown">
                                                        <button class="btn dropdown-toggle border-0 w-100 p-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <div class="media-left p-1 float-left">
                                                                <img class="distro_img media-object" src="{$operatingSystemsGroup['image']}">
                                                            </div>

                                                            <div class="media-body float-left text-left p-2">
                                                                <h4 class="distro_name media-heading">{$operatingSystemsGroup['name']}</h4>
                                                                <span id="{$group}-version" class="version">SELECT VERSION</span>
                                                            </div>
                                                        </button>

                                                        <div class="os_badge_list dropdown-menu w-100">
                                                            {foreach from=$operatingSystemsGroup['versions'] item=$operatingSystem}
                                                            <a href="#" data-os="{$operatingSystem['id']}" data-group="{$group}" onclick="AndrezzzVPS_ChooseOS(this);return false;">{$operatingSystem['name']}</a>
                                                            {/foreach}
                                                        </div>
                                                    </div>
                                                </div>
                                                {/foreach}
                                            </div>

                                            <input type="hidden" id="newOS" value="0"/>				
                                            <button onclick="AndrezzzVPS_API('Reinstall', true, { os: $_('newOS').value });" class="submit-btn">Reinstall</button>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="firewall" role="tabpanel" aria-labelledby="firewall-tab">
                                        <div class="head">
                                            <img src="{$images['settings']}">
                                            <span class="h3">Firewall</span>
                                        </div>

                                        <div class="grey-txt mb-3">The rules are evaluated from the top to the bottom. By default, everything is allowed. The firewall is only available on the public interface. Only the inbound traffic will be filtered by the firewall.</div>

                                        <div class="table-responsive">
                                            <table id="firewallTable" cellpadding="0" cellspacing="0" border="0" class="table table-hover tablesorter" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Port</th>
                                                        <th>Protocol</th>
                                                        <th>Source</th>
                                                        <th>Note</th>
                                                        <th width="50">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <select class="form-control" id="firewallAction" style="width:auto;">
                                                                <option value="ACCEPT">ACCEPT</option>
                                                                <option value="DROP">DROP</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input class="form-control" id="firewallPort" style="width:auto;" type="number" min="1" max="65535" placeholder="Port number">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" id="firewallProtocol" style="width:auto;">
                                                                <option value="ANY">ANY</option>
                                                                <option value="ICMP">ICMP</option>
                                                                <option value="TCP">TCP</option>
                                                                <option value="UDP">UDP</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input class="form-control" id="firewallSource" type="text" maxlength="128" placeholder="Ex: x.x.x.x/xx (optional)">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" id="firewallNote" type="text" maxlength="64" placeholder="Notes (optional)">
                                                        </td>
                                                        <td>
                                                            <a href="#" onclick="AndrezzzVPS_API('Add Firewall rules', true, { firewallAction: $_('firewallAction').value, protocol: $_('firewallProtocol').value, source: $_('firewallSource').value, port: $_('firewallPort').value, note: $_('firewallNote').value });return false;"><i class="fas fa-1x fa-plus create" aria-hidden="true"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <span class="badge bg-warning">The rules must be committed in order to take effect.</span>
                                        <br/>
                                        <button onclick="AndrezzzVPS_API('Commit Firewall rules');return false;" class="submit-btn">Commit Firewall</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-bootstrap-scrolling-tabs@2.6.1/dist/jquery.scrolling-tabs.min.js" integrity="sha256-CsPlYT8T5j991L8ERCDd4xLEFJn/EYUktSORO/PyTw8=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.js" integrity="sha256-t7kx8nPDixJ3ucbB9OBcTsCYhaSHvdrzJ54tfkmjjhI=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.pie.js" integrity="sha256-RsEWYd9gdLG1bCIcU8j59Rkvf/1O7HtzoD1TRUs22cU=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.time.js" integrity="sha256-pYLIMq3HE4prBar2HxbrrCdHAfG+Sv6nfnOaHDS5xBo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.stack.js" integrity="sha256-gRm10Sf18onxwOSIySMzR9kjmjQK1ejfhrDWdercOfU=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery.flot.tooltip@0.9.0/js/jquery.flot.tooltip.min.js" integrity="sha256-/hmSLSOMElnhoZ3FFQzHrF+BQWFGJBYmk2WYUX3dASc=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.29/dist/sweetalert2.all.min.js" integrity="sha256-qxQs7IPMvfbL9ZXhsZ3tG/LuZSjNxPSTBNtzk4j5FiU=" crossorigin="anonymous"></script>
        <script src="{$WEB_ROOT}/clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=DeliverFile&file=app.js"></script>
    </body>
</html>