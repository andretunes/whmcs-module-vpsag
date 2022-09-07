{**
 *	VPSAG WHMCS Server Provisioning version 1.1
 *
 *	@package     WHMCS
 *	@copyright   Andrezzz
 *	@link        https://www.andrezzz.pt
 *	@author      André Antunes <andreantunes@andrezzz.pt>
 *}

<style>
    #AndrezzzVPSPanel {
        width: 1px;
        min-width: 100%;
        border: none;
        /* display: none; */
        overflow: hidden;
        visibility: hidden;
    }
</style>

<div id="AndrezzzLoading" class="alert alert-warning" role="alert">
	<div class="notice">
		<img src="{$image}" class="mr-2">{$LANG.loading}
	</div>
</div>

<iframe id="AndrezzzVPSPanel" src="{$WEB_ROOT}/clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=Panel" scrolling="no"></iframe>

<script>
    var Loading = document.getElementById('AndrezzzLoading');
    var VPSPanel = document.getElementById('AndrezzzVPSPanel');

    function iFrameResize(){
        try {
            VPSPanel.style.height = VPSPanel.contentWindow.document.body.offsetHeight + 'px';
        } catch (e) {}
    }

    VPSPanel.onload = function() {
        Loading.style.display = 'none';
        VPSPanel.style.visibility = 'visible';

        iFrameResize();
        setInterval('iFrameResize()', 1000);
    };
</script>