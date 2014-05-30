<style type="text/css">
  .ptb-options-logo {
    background: url(https://avatars3.githubusercontent.com/u/6768662?s=140) no-repeat;
    width: 50px;
    height: 50px;
    -webkit-border-radius: 5px;
    background-size: cover;
    float: left;
    margin: 0 10px 10px 0;
  }
  .ptb-options-table {

  }
  .ptb-options-table tr:nth-child(2n) {
    background: #FCFCFC;
  }
</style>
<div class="wrap">
  <div class="ptb-options-logo"></div>
  <h2><?php echo page_type_builder()->name; ?></h2>
  <br />
  <h3>Page types</h3>
  <table class="wp-list-table widefat ptb-options-table">
    <thead>
      <tr>
        <th>
          <strong>Name</strong>
        </th>
        <th>
          <strong>Page type</strong>
        </th>
        <th>
          <strong>Template</strong>
        </th>
        <th>
          <strong>Number of pages</strong>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php
      $page_types = _ptb_get_all_page_types(true);
      foreach ($page_types as $key => $page_type) {
        ?>
        <tr>
          <td><?php echo $page_type->name; ?></td>
          <td><?php echo $page_type->page_type; ?></td>
          <td><?php
            if (!current_user_can('edit_themes') || defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
              echo $page_type->template;
            } else {
              $theme_name = basename(get_template_directory());
              $url = site_url() . '/wp-admin/theme-editor.php?file=' . $page_type->template . '&theme=' . $theme_name;
              ?>
              <a href="<?php echo $url; ?>"><?php echo $page_type->template; ?></a>
              <?php
            }
          ?></td>
          <td><?php echo _ptb_get_number_of_pages($page_type->file_name); ?></td>
        </tr>
      <?php
        }
      ?>
    </tbody>
  </table>
</div>