jQuery(document).ready(function($) {
    function fetchStatus() {
        $.ajax({
            url: BlackCnoteMonitor.restUrl,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', BlackCnoteMonitor.nonce);
            },
            success: function(data) {
                let html = '<ul style="list-style:none;padding:0;">';
                $.each(data, function(key, status) {
                    let color = status.status === 'up' ? 'green' : (status.status === 'down' ? 'red' : 'orange');
                    html += `<li><strong>${key}:</strong> <span style="color:${color}">${status.status.toUpperCase()}</span>`;
                    if (status.message) html += ` <small>(${status.message})</small>`;
                    if (status.code) html += ` <small>[HTTP ${status.code}]</small>`;
                    html += '</li>';
                });
                html += '</ul>';
                $('#blackcnote-monitor-dashboard').html(html);
            },
            error: function() {
                $('#blackcnote-monitor-dashboard').html('<span style="color:red">Failed to fetch status.</span>');
            }
        });
    }
    fetchStatus();
    setInterval(fetchStatus, 10000); // Refresh every 10s
}); 