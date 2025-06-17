<div class="modal fade" id="formGenerateModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php esc_html_e('Generate Form', HYIPLAB_PLUGIN_NAME);?></h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <i class="las la-times"></i>
          </button>
        </div>
        <form class="generate-form">
              <div class="modal-body">
                <input type="hidden" name="update_id" value="">
                <div class="form-group">
                    <label><?php esc_html_e('Form Type', HYIPLAB_PLUGIN_NAME);?></label>
                    <select name="form_type" class="form-control" required>
                        <option value=""><?php esc_html_e('Select One', HYIPLAB_PLUGIN_NAME);?></option>
                        <option value="text"><?php esc_html_e('Text', HYIPLAB_PLUGIN_NAME);?></option>
                        <option value="textarea"><?php esc_html_e('Textarea', HYIPLAB_PLUGIN_NAME);?></option>
                        <option value="select"><?php esc_html_e('Select', HYIPLAB_PLUGIN_NAME);?></option>
                        <option value="checkbox"><?php esc_html_e('Checkbox', HYIPLAB_PLUGIN_NAME);?></option>
                        <option value="radio"><?php esc_html_e('Radio', HYIPLAB_PLUGIN_NAME);?></option>
                        <option value="file"><?php esc_html_e('File', HYIPLAB_PLUGIN_NAME);?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php esc_html_e('Is Required', HYIPLAB_PLUGIN_NAME);?></label>
                    <select name="is_required" class="form-control" required>
                        <option value=""><?php esc_html_e('Select One', HYIPLAB_PLUGIN_NAME);?></option>
                        <option value="required"><?php esc_html_e('Required', HYIPLAB_PLUGIN_NAME);?></option>
                        <option value="optional"><?php esc_html_e('Optional', HYIPLAB_PLUGIN_NAME);?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php esc_html_e('Form Label', HYIPLAB_PLUGIN_NAME);?></label>
                    <input type="text" name="form_label" class="form-control" required>
                </div>
                <div class="form-group extra_area">

                </div>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn--primary text-white w-100 h-45 generatorSubmit"><?php esc_html_e('Add', HYIPLAB_PLUGIN_NAME);?></button>
              </div>
          </form>
      </div>
    </div>
</div>