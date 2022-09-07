<?php
/**
 *	VPSAG WHMCS Server Provisioning version 1.1
 *
 *	@package     WHMCS
 *	@copyright   Andrezzz
 *	@link        https://www.andrezzz.pt
 *	@author      André Antunes <andreantunes@andrezzz.pt>
 */

if (!defined('WHMCS')) {
    exit(header('Location: https://www.andrezzz.pt'));
}

use WHMCS\Config\Setting;
use WHMCS\Database\Capsule;

function AndrezzzVPSAG_API(array $params) {
    $url = 'https://www.vpsag.com/api/v1/';
    $data = [];
    $method = '';

    switch ($params['action']) {
        case 'Packages':
            $url .= 'packages';
            $method .= 'GET';
            break;

        case 'Operating Systems':
            $url .= 'os/' . $params['package'];
            $method .= 'GET';
            break;
            
        case 'Upgrades':
            $url .= 'upgrades';
            $method .= 'GET';
            break;

        case 'Discount':
            $url .= 'discount';
            $method .= 'GET';
            break;

        case 'Balance':
            $url .= 'balance';
            $method .= 'GET';
            break;
           
        case 'Order':
            $url .= 'order';
            $method .= 'POST';
            
            $billingCycles = array(
                'Monthly' => 1,
                'Quarterly' => 3,
                'Semi-Annually' => 6,
                'Annually' => 12,
                'Biennially' => 24,
                'Triennially' => 36
            );

            $data += array(
                'package' => AndrezzzVPSAG_GetOption($params, 'packageid'),
                'hostname' => $params['domain'],
                'notify_url' => Setting::getValue('SystemURL') . '/modules/servers/AndrezzzVPSAG/callback.php',
                'ram' => AndrezzzVPSAG_GetOption($params, 'extraram'),
                'disk' => AndrezzzVPSAG_GetOption($params, 'extradisk'),
                'core' => AndrezzzVPSAG_GetOption($params, 'extracore'),
                'os' => AndrezzzVPSAG_GetOption($params, 'osid'),
                'bandwidth' => AndrezzzVPSAG_GetOption($params, 'extrabandwidth'),
                'billing_term' => $billingCycles[$params['model']['billingcycle']] ?? 1,
                'ips' => AndrezzzVPSAG_GetOption($params, 'extraips'),
            );
            break;

        case 'Server Info':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag'));
            $method .= 'GET';
            break;
        
        case 'Label':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/label';
            $method .= 'POST';

            $data += array(
                'val' => $params['label'],
            );
            break;

        case 'Graphs':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/graph/' . $params['time'];
            $method .= 'GET';
            break;

        case 'Operating Systems - Server':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/os';
            $method .= 'GET';
            break;

        case 'Cancel':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/cancel';
            $method .= 'POST';

            $data += array(
                'when' => $params['when'],
            );
            break;

        case 'Stop Cancellation':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/stop-cancellation';
            $method .= 'POST';
            break;

        case 'VNC Console':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/vnc';
            $method .= 'GET';
            break;

        case 'Reinstall':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/reinstall';
            $method .= 'POST';

            $data += array(
                'os' => $params['os'],
                'notify_url' => Setting::getValue('SystemURL') . '/modules/servers/AndrezzzVPSAG/callback.php',
            );
            break;

        case 'Reboot':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/action/reboot';
            $method .= 'POST';
            break;

        case 'Stop':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/action/stop';
            $method .= 'POST';
            break;

        case 'Shutdown':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/action/shutdown';
            $method .= 'POST';
            break;

        case 'Start':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/action/start';
            $method .= 'POST';
            break;

        case 'Disable':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/action/disable';
            $method .= 'POST';
            break;

        case 'Enable':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/action/enable';
            $method .= 'POST';
            break;

        case 'IPv4 Addresses':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/ipv4';
            $method .= 'GET';
            break;

        case 'Reverse DNS':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/rdns/' . $params['ip'];
            $method .= 'POST';

            $data += array(
                'rdns' => $params['rdns']
            );
            break;

        case 'Addons':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/addons';
            $method .= 'GET';
            break;

        case 'Upgrade':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/upgrade';
            $method .= 'POST';

            $data += array(
                'ram' => AndrezzzVPSAG_GetOption($params, 'extraram'),
                'disk' => AndrezzzVPSAG_GetOption($params, 'extradisk'),
                'core' => AndrezzzVPSAG_GetOption($params, 'extracore'),
                'bandwidth' => AndrezzzVPSAG_GetOption($params, 'extrabandwidth'),
                'ips' => AndrezzzVPSAG_GetOption($params, 'extraips'),
            );
            break;

        case 'Hostname rDNS':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/hostname';
            $method .= 'POST';

            $data += array(
                'hostname' => $params['hostname']
            );
            break;

        case 'Create backup':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/backup';
            $method .= 'POST';
            break;

        case 'Delete backup':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/backup/' . $params['file'];
            $method .= 'DELETE';
            break;

        case 'List backups':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/backup';
            $method .= 'GET';
            break;

        case 'Restore backup':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/restore/' . $params['file'];
            $method .= 'POST';
            break;

        case 'Get Firewall rules':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/firewall';
            $method .= 'GET';
            break;

        case 'Add Firewall rules':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/firewall';
            $method .= 'POST';

            $data += array(
                'action' => $params['firewallAction'],
                'protocol' => $params['protocol'],
                'source' => $params['source'],
                'port' => $params['port'],
                'note' => $params['note'],
            );
            break;

        case 'Delete Firewall rule':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/firewall/' . $params['rule_id'];
            $method .= 'DELETE';
            break;

        case 'Commit Firewall rules':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/firewall/resync';
            $method .= 'POST';
            break;

        case 'ISO Images':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/iso';
            $method .= 'GET';
            break;

        case 'Load ISO':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/iso/' . $params['iso_id'];
            $method .= 'POST';
            break;

        case 'Eject ISO':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/iso/0';
            $method .= 'POST';
            break;

        case 'Reset root':
            $url .= 'vps/' . str_replace('VPSAG-', '', $params['model']->serviceProperties->get('vpsag')) . '/reset-root';
            $method .= 'POST';
            break;

        default:
            throw new Exception('Invalid action: ' . $params['action']);
            break;
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTREDIR, CURL_REDIR_POST_301);
    curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Andrezzz - VPSAG WHMCS');
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('X_API_USER: ' . $params['serverusername'], 'X_API_KEY:  ' . $params['serverpassword']));

    if ($method === 'POST' || $method === 'PATCH') {
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $responseData = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    $responseData = json_decode($responseData, true);

    if ($statusCode === 0) throw new Exception('cURL Error: ' . curl_error($curl));

    curl_close($curl);

    logModuleCall(
        'Andrezzz - VPSAG',
        $url,
        !empty($data) ? json_encode($data) : '',
        print_r($responseData, true)
    );

    if (isset($responseData['status']) && $responseData['status'] === 0) throw new Exception($responseData['result']);

    return $responseData['result'];
}

function AndrezzzVPSAG_Error($func, $params, Exception $err) {
    logModuleCall('Andrezzz - VPSAG', $func, $params, $err->getMessage(), $err->getTraceAsString());
}

function AndrezzzVPSAG_MetaData() {
    return array(
        'DisplayName' => 'Andrezzz - VPSAG',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
    );
}

function AndrezzzVPSAG_ConfigOptions() {
    $error = array(
        'error' => array(
            'FriendlyName' => 'Error',
            'Description' => 'Please double check if you selected a Server Group and/or your details are correct.',
            'Type' => '',
        ),
    );

    $array = array(
        'packageid' => array(
            'FriendlyName' => 'Package',
            'Description' => 'The Package desired (Configurable option: packageid).',
            'Type' => 'dropdown',
            'Options' => array(),
        ),
        'osid' => array(
            'FriendlyName' => 'Operating System',
            'Description' => 'The Operating System desired (Configurable option: osid).',
            'Type' => 'dropdown',
            'Options' => array(),
        ),
        'extracore' => array(
            'FriendlyName' => 'Extra Cores',
            'Description' => 'Min is {$min} and max is {$max}, to disable it is 0 (Configurable option: extracore).',
            'Type' => 'text',
            'Default' => '0',
            'Size' => '4',
        ),
        'extraram' => array(
            'FriendlyName' => 'Extra RAM',
            'Description' => 'Min is {$min} and max is {$max}, to disable it is 0 (Configurable option: extraram).',
            'Type' => 'text',
            'Default' => '0',
            'Size' => '4',
        ),
        'extradisk' => array(
            'FriendlyName' => 'Extra Disk',
            'Description' => 'Min is {$min} and max is {$max}, to disable it is 0 (Configurable option: extradisk).',
            'Type' => 'text',
            'Default' => '0',
            'Size' => '4',
        ),
        'extrabandwidth' => array(
            'FriendlyName' => 'Extra Bandwidth',
            'Description' => 'Min is {$min} and max is {$max}, to disable it is 0 (Configurable option: extrabandwidth).',
            'Type' => 'text',
            'Default' => '0',
            'Size' => '4',
        ),
    );
    
    try {
        if (basename($_SERVER['SCRIPT_NAME'], '.php') === 'configproducts' && ($_REQUEST['action'] === 'module-settings' || $_POST['action'] === 'module-settings')) {
            $id = 0;
            $product;
            $serverGroup = 0;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = (int) $_POST['id'];

                $product = Capsule::table('tblproducts')->where('id', $id)->first();
                $serverGroup = (int) $_POST['servergroup'];
            } else {
                $id = (int) $_REQUEST['id'];
        
                $product = Capsule::table('tblproducts')->where('id', $id)->first();
                $serverGroup = (int) $product->servergroup;
            }
            
            $serverGroup = Capsule::table('tblservergroupsrel')->where('groupid', $serverGroup)->first();
            if (!$serverGroup) throw new Exception('No server group specified.');

            $server = Capsule::table('tblservers')->where('id', $serverGroup->serverid)->first();
            if (!$server) throw new Exception('No server found in the server group.');
        
            $params = array(
                'serverusername' => $server->username,
                'serverpassword' => decrypt($server->password),
            );
    
            $params['action'] = 'Packages';
            $packageslist = AndrezzzVPSAG_API($params);
        
            foreach ($packageslist as $package) {
                $array['packageid']['Options'] += array(
                    $package['id'] => $package['name'] . ' (' . $package['price'] . '€)'
                );
            }
            
            if ($product->configoption1 == '') return $array;
        
            $params['action'] = 'Operating Systems';
            $params['package'] = $product->configoption1;
            $operatingSystems = AndrezzzVPSAG_API($params);
        
            foreach ($operatingSystems as $operatingSystem) {
                $array['osid']['Options'] += array(
                    $operatingSystem['id'] => $operatingSystem['name']
                );
            }
        
            $params['action'] = 'Upgrades';
            $upgrades = AndrezzzVPSAG_API($params);
        
            foreach ($upgrades as $upgrade) {
                $description = $array['extra' . $upgrade['id']]['Description'];
                $description = str_replace('{$min}', $upgrade['unit'] . ' ' . $upgrade['type'], $description);
                $description = str_replace('{$max}', $upgrade['max'] . ' ' . $upgrade['type'], $description);
                
                $array['extra' . $upgrade['id']]['Description'] = $description;
                $array['extra' . $upgrade['id']]['FriendlyName'] .= ' (' . $upgrade['price'] . '€)';
            }
        }
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);

        $error['error']['Description'] = 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
        return $error;
    }
    
    return $array;
}

function AndrezzzVPSAG_GetOption(array $params, $id, $default = NULL) {
    $options = AndrezzzVPSAG_ConfigOptions();

    $friendlyName = $options[$id]['FriendlyName'];

    if (isset($params['configoptions'][$friendlyName]) && $params['configoptions'][$friendlyName] !== '') {
        return $params['configoptions'][$friendlyName];
    } else if (isset($params['configoptions'][$id]) && $params['configoptions'][$id] !== '') {
        return $params['configoptions'][$id];
    } else if (isset($params['customfields'][$friendlyName]) && $params['customfields'][$friendlyName] !== '') {
        return $params['customfields'][$friendlyName];
    } else if (isset($params['customfields'][$id]) && $params['customfields'][$id] !== '') {
        return $params['customfields'][$id];
    }

    $found = false;
    $i = 0;
    
    foreach ($options as $key => $value) {
        $i++;
        if ($key === $id) {
            $found = true;
            break;
        }
    }

    if ($found && isset($params['configoption' . $i]) && $params['configoption' . $i] !== '') {
        return $params['configoption' . $i];
    }

    return $default;
}

function AndrezzzVPSAG_TestConnection(array $params) {
    $err = '';

    try {
        $params['action'] = 'Balance';
        AndrezzzVPSAG_API($params);
    } catch(Exception $e) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $e);
        $err = 'Received the error: ' . $e->getMessage() . ' Check module debug log for more detailed error.';
    }

    return [
        'success' => $err === '',
        'error' => $err,
    ];
}

function AndrezzzVPSAG_CreateAccount(array $params) {
    try {
        $params['action'] = 'Order';
        $create = AndrezzzVPSAG_API($params);

        $params['model']->serviceProperties->save([
            'vpsag|VPSAG ID' => 'VPSAG-' . $create['vps_id'],
        ]);
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
    }
    
    return 'success';
}

function AndrezzzVPSAG_SuspendAccount(array $params) {
    try {
        $params['action'] = 'Disable';
        AndrezzzVPSAG_API($params);
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
    }

    return 'success';
}

function AndrezzzVPSAG_UnsuspendAccount(array $params) {
    try {
        $params['action'] = 'Enable';
        AndrezzzVPSAG_API($params);
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
    }

    return 'success';
}

function AndrezzzVPSAG_TerminateAccount(array $params) {
    try {
        $params['action'] = 'Cancel';
        $params['when'] = 'now';
        AndrezzzVPSAG_API($params);
        
        Capsule::table('tblhosting')->where('id', $params['serviceid'])->update(array(
            'username' => '',
            'password' => '',
        ));
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
    }

    return 'success';
}

// function AndrezzzVPSAG_ChangePassword(array $params) {
//     try {
//         $params['action'] = 'ChangePassword';
//         AndrezzzVPSAG_API($params);
//     } catch(Exception $err) {
//         AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
//         return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
//     }

//     return 'success';
// }

function AndrezzzVPSAG_ChangePackage(array $params) {
    try {
        $params['action'] = 'Upgrade';
        AndrezzzVPSAG_API($params);
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
    }

    return 'success';
}

function AndrezzzVPSAG_Start(array $params) {
    try {
        AndrezzzVPSAG_API($params);
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
    }

    return 'success';
}

function AndrezzzVPSAG_Reboot(array $params) {
    try {
        AndrezzzVPSAG_API($params);
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
    }

    return 'success';
}

function AndrezzzVPSAG_Stop(array $params) {
    try {
        AndrezzzVPSAG_API($params);
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
    }

    return 'success';
}

function AndrezzzVPSAG_VNC(array $params) {
    try {
        $params['action'] = 'VNC Console';
        $vnc = AndrezzzVPSAG_API($params);

        echo '<style>body{margin: 0px;}</style><iframe src="' . $vnc['vnc_url'] . '" scrolling="none" height="100%" width="100%" frameborder="0"></iframe>';
        // header('Location: ' . $vnc['vnc_url']);
        WHMCS\Terminus::getInstance()->doExit();
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);

        return array(
            'templatefile' => 'template/error',
            'templateVariables' => array(
                'error' => $err->getMessage(),
            ),
        );
    }
}

function AndrezzzVPSAG_ClientAreaAPI(array $params) {
    try {
        $action = App::getFromRequest('api');
        $actions = array('Server Info', 'Graphs', 'Reinstall', 'Reboot', 'Stop', 'Start', 'IPv4 Addresses', 'Hostname rDNS', 'Create backup', 'Delete backup', 'List backups', 'Get Firewall rules', 'Add Firewall rules', 'Delete Firewall rule', 'Commit Firewall rules', 'ISO Images', 'Load ISO', 'Eject ISO', 'Reset root');
        $results = array('result' => 'success');

        if (in_array($action, $actions)) {
            foreach ($_POST as $key => $value) {
                $params[$key] = $value;
            }

            $params['action'] = $action;
            $result = AndrezzzVPSAG_API($params);
            $results = array_merge($results, is_array($result) ? $result : array('data' => $result));

            return array('jsonResponse' => $results);
        } else {
            throw new Exception('Action not found');
        }
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return array('jsonResponse' => array('result' => 'error', 'message' => $err->getMessage()));
    }
}

function AndrezzzVPSAG_DeliverFile(array $params) {
    try {
        $dir = __DIR__ . '/template/';
        $file = App::getFromRequest('file');
        $files = array('app.min.css', 'app.min.js');

        if (in_array($file, $files)) {
            $type = '';

			if (function_exists('ob_gzhandler')) {
				ob_start('ob_gzhandler');
			}
            
            if (strpos($file, '.js') !== false) {
                $dir .= 'js/';
                $type = 'application/javascript';
            } else if (strpos($file, '.css') !== false) {
                $dir .= 'css/';
                $type = 'text/css';
            } else {
                $type = 'text/html';
            }

            header('Content-Type: ' . $type . '; charset=utf-8');
            header('Cache-Control: max-age=604800, public');
            
            echo file_get_contents($dir . $file);
            WHMCS\Terminus::getInstance()->doExit();
        } else {
            throw new Exception('File not found');
        }
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return array('jsonResponse' => array('result' => 'error', 'message' => $err->getMessage()));
    }
}

function AndrezzzVPSAG_Panel(array $params) {
    try {
        $params['action'] = 'Server Info';
        $serverInfo = AndrezzzVPSAG_API($params);

        $params['action'] = 'Operating Systems - Server';
        $operatingSystemsTemp = AndrezzzVPSAG_API($params);

        $dirImages = __DIR__ . '/template/img/';
        $availableImages = glob($dirImages . '*.png');
        $images = array();
        
        foreach ($availableImages as $key => $image) {
            $images[explode('.png', explode($dirImages, $image)[1])[0]] = 'data:image/png;base64,' . base64_encode(file_get_contents($image));
        }

        $dirOS = __DIR__ . '/template/img/os/';
        $availableOS = glob($dirOS . '*.png');
        $operatingSystems = array();
        
        foreach ($availableOS as $key => $os) {
            $availableOS[$key] = explode('.png', explode($dirOS, $os)[1])[0];
        }

        foreach ($operatingSystemsTemp as $key => $operatingSystem) {
            $group = $operatingSystem['group'];
            
            if (!isset($operatingSystems[$group])) {
                $image = file_get_contents($dirOS . (in_array($group, $availableOS) ? $group : 'others') . '.png');
                
                $operatingSystems[$group] = array(
                    'name' => $operatingSystem['group_name'],
                    'image' => 'data:image/png;base64,' . base64_encode($image),
                    'versions' => array(),
                );
            }
            
            $operatingSystems[$group]['versions'][] = $operatingSystem;
        }
        
        $serverInfo['operatingSystem'] = $serverInfo['os'];
        $serverInfo['operatingSystem'] = array_search($serverInfo['operatingSystem'], array_column($operatingSystemsTemp, 'id'));
        $serverInfo['operatingSystem'] = $operatingSystemsTemp[$serverInfo['operatingSystem']];
        $serverInfo['operatingSystem'] = $operatingSystems[$serverInfo['operatingSystem']['group']];

        $serverInfo['status'] = $serverInfo['status'] !== 'ok' ? $serverInfo['status'] : $serverInfo['vm_status'];
        $serverInfo['statusImage'] = $images[$serverInfo['status']];
        $serverInfo['statusDescription'] = ucfirst($serverInfo['status']);
        
        global $_LANG;
        $smarty = new WHMCS\Smarty();
        $assetHelper = DI::make('asset');

        $smarty->assign('LANG', $_LANG);
        $smarty->assign('WEB_ROOT', $assetHelper->getWebRoot());
        $smarty->assign('serviceid', $params['serviceid']);

        $smarty->assign('images', $images);
        $smarty->assign('serverInfo', $serverInfo);
        $smarty->assign('operatingSystems', $operatingSystems);
        
        $html = $smarty->fetch(__DIR__ . '/template/clientarea.tpl');
        echo $html;
        
        WHMCS\Terminus::getInstance()->doExit();
    } catch (Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);

        $smarty = new WHMCS\Smarty();
        
        $smarty->assign('error', $err->getMessage());
        $smarty->assign('image', 'data:image/png;base64,' . base64_encode(file_get_contents(__DIR__ . '/template/img/notice.png')));
        
        $html = $smarty->fetch(__DIR__ . '/template/error.tpl');
        echo $html;
        
        WHMCS\Terminus::getInstance()->doExit();
    }
}

function AndrezzzVPSAG_AdminCustomButtonArray() {
    return array(
        'Start' => 'Start',
        'Stop'=> 'Stop',
        'Reboot' => 'Reboot',
        'VNC Console'=> 'VNC',
	);
}

function AndrezzzVPSAG_ClientAreaCustomButtonArray() {
    return array(
        'Start' => 'Start',
        'Stop'=> 'Stop',
        'Reboot' => 'Reboot',
        'VNC Console'=> 'VNC',
	);
}

function AndrezzzVPSAG_ClientAreaAllowedFunctions() {
    return array('ClientAreaAPI', 'DeliverFile', 'Panel');
}

function AndrezzzVPSAG_ClientArea(array $params) {
    if ($params['moduletype'] !== 'AndrezzzVPSAG') return;

    try {
        return array(
            'templatefile' => 'template/iframe',
            'templateVariables' => array(
                'image' => 'data:image/png;base64,' . base64_encode(file_get_contents(__DIR__ . '/template/img/notice.png'))
            ),
        );
    } catch (Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);

        return array(
            'templatefile' => 'template/error',
            'templateVariables' => array(
                'error' => $err->getMessage(),
                'image' => 'data:image/png;base64,' . base64_encode(file_get_contents(__DIR__ . '/template/img/notice.png'))
            ),
        );
    }
}

function AndrezzzVPSAG_AdminLink(array $params) {
    try {
        $params['action'] = 'Balance';
        $balance = AndrezzzVPSAG_API($params);

        $params['action'] = 'Discount';
        $discount = AndrezzzVPSAG_API($params);

        return '<i class="fa fa-coins"></i> Balance: ' . $balance['balance'] . '€<br><i class="fa fa-badge-percent"></i> Discount: ' . $discount['percent'] . '%';
    } catch(Exception $err) {
        AndrezzzVPSAG_Error(__FUNCTION__, $params, $err);
        return 'Received the error: ' . $err->getMessage() . ' Check module debug log for more detailed error.';
    }
}