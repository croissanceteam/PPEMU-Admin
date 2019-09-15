
<footer class="main-footer">
<div class="pull-right hidden-xs">
  <b>Version</b> 1.0.0
</div>
<strong>Copyright &copy; 2016-2019 <a href="http://www.croissancehub.com">Croissance Hub</a>.</strong> All rights
reserved.
</footer>

<div class="modal" id="newPassModal" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Changement de mon mot de passe</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
        <!-- /.modal-footer -->
      </form>
    </div>
  </div>
</div>

<!-- NEW USER-->
<div class="modal" id="newUserModal" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Nouvel utilisateur</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="new-user-form">
        <div class="modal-body">
          <div class="row">
          <div class="form-group col-md-6">
          <input type="hidden" class="form-control" name="add" >
            <label class="col-form-label required">Nom d'utilisateur</label>
            <input type="text" class="form-control" name="username" placeholder="Nom d'utilisateur"  required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label required">Nom complet</label>
            <input type="text" class="form-control" name="fullname" placeholder="Nom complet" required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label">Numéro de téléphone</label>
            <input type="text" class="form-control" name="phone" placeholder="Numéro de téléphone" >
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label required">Adresse e-mail</label>
            <input type="email" class="form-control" name="email" placeholder="Adresse e-mail" required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label">Ville</label>
            <input type="text" class="form-control" name="town" placeholder="Ville de résidence" required>
          </div>
          <div class="form-group col-md-6">
            
            <input type="checkbox" name="status" class="js-switch"  checked />
            <label class="col-form-label" id="status-label">Actif</label>
          </div>
          </div>
          <!-- /.row -->
          
        </div>
        <!-- /.modal-body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Enregistrer</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
        <!-- /.modal-footer -->
      </form>
    </div>
  </div>
</div>

<!-- UPDATE-->
<div class="modal" id="updateUserModal" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Nouvel utilisateur</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="new-user-form">
        <div class="modal-body">
          <div class="row">
          <div class="form-group col-md-6">
          <input type="hidden" class="form-control" name="add" >
            <label class="col-form-label required">Nom d'utilisateur</label>
            <input type="text" class="form-control" name="username" id="username" placeholder="Nom d'utilisateur"  required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label required">Nom complet</label>
            <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Nom complet" required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label">Numéro de téléphone</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="Numéro de téléphone" >
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label required">Adresse e-mail</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Adresse e-mail" required>
          </div>
          <div class="form-group col-md-6">
            <label class="col-form-label">Ville</label>
            <input type="text" class="form-control" name="town" id="town" placeholder="Ville de résidence" required>
          </div>
          <div class="form-group col-md-6">
            
            <input type="checkbox" name="status" id="status" class="js-switch"  checked />
            <label class="col-form-label" id="status-label">Actif</label>
          </div>
          </div>
          <!-- /.row -->
          
        </div>
        <!-- /.modal-body -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Enregistrer</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
        <!-- /.modal-footer -->
      </form>
    </div>
  </div>
</div>