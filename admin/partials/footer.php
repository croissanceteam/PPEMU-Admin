
<footer class="main-footer">
<div class="pull-right hidden-xs">
  <!-- <b>Version</b> 1.0.0 -->
</div>
<strong>Copyright &copy; 2019 <a href="http://www.croissancehub.com">Croissance Hub</a>.</strong> All rights
reserved.
</footer>

<div class="modal" id="newPassModal" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Changement de mon mot de passe</h4>
        <button type="button" class="close hidden" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="change-password-form">
        <div class="modal-body">
          <div class="form-group">
            <label class="col-form-label">Mot de passe actuel</label>
            <input type="password" class="form-control" name="actual-password" id="actual-password" placeholder="Mot de passe actuel" required>
            <input type="text" class="hidden" name="username" id="username" value="<?= $_SESSION['pseudoPsv'] ?>">
          </div>
          <div class="form-group">
            <label class="col-form-label">Nouveau mot de passe</label>
            <input type="password" class="form-control" name="new-password" id="new-password" placeholder="Taper le nouveau mot de passe" required>
          </div>
          <div class="form-group">
            <label class="col-form-label">Nouveau mot de passe</label>
            <input type="password" class="form-control" name="new-password-again" id="new-password-again" placeholder="Retaper le nouveau mot de passe" required>
          </div>
        </div>
        <!-- /.modal-body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Enregistrer le changement</button>
          <button type="reset" class="btn btn-default" data-dismiss="modal" onclick="this.form.reset();">Annuler</button>
        </div>
        <!-- /.modal-footer -->
      </form>
    </div>
  </div>
</div>

