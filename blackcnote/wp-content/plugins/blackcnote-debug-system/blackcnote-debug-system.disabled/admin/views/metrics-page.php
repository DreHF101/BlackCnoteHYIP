<div class="wrap">
    <h1>Metrics</h1>
    <p>Prometheus metrics are available at <a href="http://localhost:9091/metrics" target="_blank">http://localhost:9091/metrics</a></p>
    <div id="blackcnote-metrics-summary">Loading metrics...</div>
    <script>
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function($) {
                function fetchMetrics() {
                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'blackcnote_debug_metrics',
                            nonce: BlackCnoteMonitor.nonce
                        },
                        success: function(response) {
                            if (response.success && response.data) {
                                let html = '<ul style="list-style:none;padding:0;">';
                                $.each(response.data, function(key, value) {
                                    html += `<li><strong>${key}:</strong> <span>${value}</span></li>`;
                                });
                                html += '</ul>';
                                $('#blackcnote-metrics-summary').html(html);
                            }
                        },
                        error: function() {
                            $('#blackcnote-metrics-summary').html('<span style="color:red">Failed to fetch metrics.</span>');
                        }
                    });
                }
                fetchMetrics();
                setInterval(fetchMetrics, 10000);
            });
        }
    </script>
</div> 