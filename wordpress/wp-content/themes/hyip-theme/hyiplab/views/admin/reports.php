<?php
hyiplab_layout('admin/layouts/master');
?>
<div class="row mb-none-30">
  <div class="col-md-12">
    <div class="card b-radius--10 ">
      <div class="card-body p-0">
        <div class="table-responsive--md  table-responsive">
          <table class="table table--light style--two">
            <thead>
              <tr>
                <th><?php esc_html_e('Type', HYIPLAB_PLUGIN_NAME); ?></th>
                <th><?php esc_html_e('Message', HYIPLAB_PLUGIN_NAME); ?></th>
                <th><?php esc_html_e('Status', HYIPLAB_PLUGIN_NAME); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reports as $report) { ?>
                <tr>
                  <td><?php echo esc_html($report->req_type)?></td>
                  <td class="text-center white-space-wrap"><?php echo esc_html($report->message);?></td>
                  <td><span class="badge badge--<?php echo esc_html($report->status_class);?>"><?php echo esc_html($report->status_text);?></span></td>
                </tr>
              <?php } ?>
              <?php if (hyiplab_check_empty($reports)) { ?>
                <tr>
                  <td colspan="100%" class="text-center"><?php esc_html_e('Data Not Found', HYIPLAB_PLUGIN_NAME); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table><!-- table end -->
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="bugModal" tabindex="-1" role="dialog" aria-labelledby="bugModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bugModalLabel"><?php esc_html_e('Report & Request', HYIPLAB_PLUGIN_NAME); ?></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="las la-times"></i>
        </button>
      </div>
      <form action="<?php echo hyiplab_route_link('admin.request.report.submit') ?>" method="post">
        <?php hyiplab_nonce_field('admin.request.report.submit') ?>
        <div class="modal-body">
          <div class="form-group">
            <label><?php esc_html_e('Type', HYIPLAB_PLUGIN_NAME); ?></label>
            <select class="form-control" name="type" required>
              <option value="bug" <?php selected(hyiplab_old("type"), "bug") ?>><?php esc_html_e('Report Bug', HYIPLAB_PLUGIN_NAME); ?></option>
              <option value="feature" <?php selected(hyiplab_old("type"), "feature") ?>><?php esc_html_e('Feature Request', HYIPLAB_PLUGIN_NAME); ?></option>
            </select>
          </div>
          <div class="form-group">
            <label><?php esc_html_e('Message', HYIPLAB_PLUGIN_NAME); ?></label>
            <textarea class="form-control" name="message" rows="5" required><?php echo hyiplab_old('message') ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn--primary w-100 h-45"><?php esc_html_e('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
$html = '<button class="btn btn-sm btn-outline--primary" data-bs-toggle="modal" data-bs-target="#bugModal"><i class="las la-bug"></i> Report a bug</button>
    <a href="https://viserlab.com/support" target="_blank" class="btn btn-sm btn-outline--primary"><i class="las la-headset"></i> Request for Support</a>';
hyiplab_push_breadcrumb($html);
?>