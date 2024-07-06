<?php


class Notifier {
    // la class n'envoie pas de mail, elle affiche juste un message (comme vous l'aviez demandé)

    // Envoie un email de confirmation d'inscription
    public function sendSignupConfirmation($to, $otp) {
        $subject = 'Confirmation de votre inscription';
        $body = "Veuillez saisir le code suivant pour confirmer votre inscription (dans le formulaire qui envoie ensuite la requète POST) : $otp";
        return $this->sendNotification($to, $subject, $body);
    }

    // Envoie un email de réinitialisation de mot de passe
    public function sendPasswordReset($to, $otp) {
        $subject = 'Réinitialisation de votre mot de passe';
        $body = "Veuillez saisir le code suivant pour réinitialiser votre mot de passe (dans le formulaire qui envoie ensuite la requète POST) : $otp";
        return $this->sendNotification($to, $subject, $body);
    }

    // Envoie un email de suppression de compte
    public function sendAccountDelete($to, $otp) {
        $subject = 'Suppression de votre compte';
        $body = "Veuillez saisir le code suivant pour supprimer votre compte (dans le formulaire qui envoie ensuite la requète POST) : $otp";
        return $this->sendNotification($to, $subject, $body);
    }

    // Envoie un email de notification
    public function sendNotification($to, $subject, $body) {
        echo "<br>";
        echo "Email envoyé à $to";
        echo "<br>";
        echo "Sujet : $subject";
        echo "<br>";
        echo "Contenu du mail : $body";
    }
}
?>
