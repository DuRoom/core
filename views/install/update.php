<h2>Update DuRoom</h2>

<p>Enter your database password to update DuRoom. Before you proceed, you should <strong>back up your database</strong>. If you have any trouble, get help on the <a href="http://duroom.js.org/docs/update.html" target="_blank">DuRoom website</a>.</p>

<form method="post">
  <div id="error" style="display:none"></div>

  <div class="FormGroup">
    <div class="FormField">
      <label>Database Password</label>
      <input type="password" name="databasePassword">
    </div>
  </div>

  <div class="FormButtons">
    <button type="submit">Update DuRoom</button>
  </div>
</form>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>
$(function() {
  $('form :input:first').select();

  $('form').on('submit', function(e) {
    e.preventDefault();

    var $button = $(this).find('button')
      .text('Please Wait...')
      .prop('disabled', true);

    $.post('', $(this).serialize())
      .done(function() {
        window.location.reload();
      })
      .fail(function(data) {
        $('#error').show().text('Something went wrong:\n\n' + data.responseText);

        $button.prop('disabled', false).text('Update Flarum');
      });

    return false;
  });
});
</script>
