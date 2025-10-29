<form method="POST" action="?page=submit_avis">
    <input type="hidden" name="id_reservation" value="<?= $id_reservation ?>">
    <label>Le covoiturage s'est-il bien passé ?</label><br>
    <select name="note" required>
        <option value="">-- Note --</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select><br>
    <label>Commentaire :</label><br>
    <textarea name="commentaire" rows="3" cols="50"></textarea><br>
    <button type="submit" class="btn btn-primary mt-2">Soumettre</button>
</form>
