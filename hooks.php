<?php


function whmcslack_getConfig()
{
    $q = select_query('tbladdonmodules', 'value', array('module' => 'whmcslack', 'setting' => 'webhook'));
    if (mysql_num_rows($q) == 0)
        return null;
    $r = [];
    while ($d = mysql_fetch_array($q)) {
        $r[$d['setting']] = $d['value'];
    }
}

function whmcslack_call($webhookUrl, $data)
{
    $payload = ['payload' => json_encode($data)];
    $response = curlCall($webhookUrl, $payload);
}

function whmcslack_ClientAdd($vars)
{
    global $customadminpath, $CONFIG;
    $conf = whmcslack_config();
    if ($conf == null || empty($conf['webhook']))
        return;
    if (!$conf['new_client'])
        return;
    $url = $CONFIG['SystemURL'] . '/' . $customadminpath . '/clientssummary.php?userid=' . $vars['userid'];
    $data = [];
    $data['text'] = 'A new client has signed up! ' .
        '<' . $url . '|Click here> for details!';
    whmcslack_call($conf['webhook'], $data);
}

function whmcslack_InvoicePaid($vars)
{
    global $customadminpath, $CONFIG;
    $conf = whmcslack_config();
    if ($conf == null || empty($conf['webhook']))
        return;
    if (!$conf['new_invoice'])
        return;
    $url = $CONFIG['SystemURL'] . '/' . $customadminpath . '/invoices.php?action=edit&id=' . $vars['invoiceid'];
    $data = [];
    $data['text'] = 'Invoice ' . $vars['invoiceid'] . ' has just been paid! ' .
        '<' . $url . '|Click here> for details!';
    whmcslack_call($conf['webhook'], $data);
}

function whmcslack_TicketOpen($vars)
{
    global $customadminpath, $CONFIG;
    $conf = whmcslack_config();
    if ($conf == null || empty($conf['webhook']))
        return;
    if (!$conf['new_ticket'])
        return;
    $url = $CONFIG['SystemURL'] . '/' . $customadminpath . '/supporttickets.php?action=viewticket&id=' . $vars['ticketid'];
    $data = [];
    $data['text'] = 'New ticket: _' . $vars['subject'] . '_ (in ' . $vars['deptname'] . ') ' .
        '<' . $url . '|Click here> for details!';
    whmcslack_call($conf['webhook'], $data);
}

function whmcslack_TicketUserReply($vars)
{
    global $customadminpath, $CONFIG;
    $conf = whmcslack_config();
    if ($conf == null || empty($conf['webhook']))
        return;
    if (!$conf['new_update'])
        return;
    $url = $CONFIG['SystemURL'] . '/' . $customadminpath . '/supporttickets.php?action=viewticket&id=' . $vars['ticketid'];
    $data = [];
    $data['text'] = 'Ticket #' . $vars['ticketid'] . ' has been updated _' . $vars['subject'] . '_ (in ' . $vars['deptname'] . ') ' .
        '<' . $url . '|Click here> for details!';
    whmcslack_call($conf['webhook'], $data);
}


add_hook("ClientAdd", 1, "whmcslack_ClientAdd");
add_hook("InvoicePaid", 1, "whmcslack_InvoicePaid");
add_hook("TicketOpen", 1, "whmcslack_TicketOpen");
add_hook("TicketUserReply", 1, "whmcslack_TicketUserReply");
