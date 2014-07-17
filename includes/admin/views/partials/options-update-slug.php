  <div class="ptb-options-postbox-outer">
    <h3>Update property slug</h3>
    <p class="ptb-options-description description">Rename property slug with a new slug. Will update all pages.</p>
    <div class="postbox ptb-options-postbox">
      <div class="inside">
        <form method="post" action="options.php">
          <label for="ptb_page_type">Page type</label>
          <select name="ptb_page_type">
            <?php foreach ($page_types as $page_type): ?>
              <option data-page-type="<?php echo $page_type->file_name; ?>" value="<?php echo $page_type->file_name; ?>">
                <?php echo $page_type->name; ?>
              </option>
            <?php endforeach; ?>
          </select>
          <label for="ptb_old_slug">Old property slug</label>
          <input type="text" name="ptb_old_slug" />
          <label for="ptb_new_slug">New property slug</label>
          <select id="ptb_new_slug_container" class="hidden">
            <?php
              foreach ($page_types as $page_type):
                $klass = new $page_type->page_type;
                $properties = $klass->get_properties();
                foreach ($properties as $property): ?>
                  <option data-page-type="<?php echo $page_type->file_name; ?>" value="<?php echo $property->slug; ?>">
                    <?php echo _ptb_remove_ptb($property->slug); ?>
                  </option>
            <?php
                endforeach;
              endforeach;
            ?>
          </select>
          <select name="ptb_new_slug">
            <?php
              $page_type = $page_types[0];
              if (is_object($page_type)):
                $klass = new $page_type->page_type;
                $properties = $klass->get_properties();
                foreach ($properties as $property): ?>
                  <option data-page-type="<?php echo $page_type->file_name; ?>" value="<?php echo $property->slug; ?>">
                    <?php echo _ptb_remove_ptb($property->slug); ?>
                  </option>
              <?php
                endforeach;
              endif;
              ?>
          </select>
          <br />
          <br />
          <input type="submit" class="button button-primary" value="Update" />
        </form>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    (function ($) {

      /**
       * Update new slug select with options based on page type.
       */

      $('select[name=ptb_page_type]').on('change', function (e) {
        e.preventDefault();

        var $slugContainer = $('#ptb_new_slug_container')
          , $newSlug = $('select[name=ptb_new_slug]')
          , pageType = $('option:selected', this).attr('data-page-type');

        $newSlug.empty();

        $slugContainer.find('option[data-page-type="' + pageType + '"]').clone().appendTo($newSlug);
      });

    })(window.jQuery);
  </script>