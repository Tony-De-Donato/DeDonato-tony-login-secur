# DE DONATO Tony - 2024
# README - Projet LOGIN

Ce README fournit des informations essentielles pour comprendre, configurer et utiliser le login 




## Introduction

    Ce projet vise à fournir differents endpoints pour la gestion des utilisateurs et de leurs sessions.
    Pour l'utiliser, il suffit de placer le dossier www et le dossier configs dans le dossier wamp64 (ou équivalent).
    Ensuite, il suffit de lancer le serveur et de faire des requètes POST sur les différents endpoints.


## Fonctionnalités

    Les différents endpoints sont les suivants:
        - /Securite/SignUp/ : Permet de créer un utilisateur
            Paramètres de la requète: 
                email: le login de l'utilisateur
                password: le mot de passe de l'utilisateur

        - /Securite/ConfirmSignUp/ : Permet de confirmer l'inscription d'un utilisateur
            Paramètres de la requète: 
                email: le login de l'utilisateur
                otp: l'otp fournit lors du SignUp
        
        - /Securite/DeleteAccount/ : Permet de supprimer un utilisateur
            Paramètres de la requète: 
                email: le login de l'utilisateur

        - /Securite/ConfirmDelete/ : Permet de confirmer la suppression d'un utilisateur
            Paramètres de la requète: 
                email: le login de l'utilisateur
                otp: l'otp fournit lors du DeleteAccount

        - /Securite/ChangePassword/ : Permet de changer le mot de passe d'un utilisateur
            Paramètres de la requète: 
                email: le login de l'utilisateur
                password: le nouveau mot de passe de l'utilisateur

        - /Securite/ConfirmChangePassword/ : Permet de confirmer le changement de mot de passe d'un utilisateur
            Paramètres de la requète: 
                email: le login de l'utilisateur
                otp: l'otp fournit lors du ChangePassword


## Postman

    Pour tester les différents endpoints, vous pouvez utiliser le fichier login_tests.postman_collection.json fournit.
    Il contient des exemples de requètes pour chaque endpoint.


## Base de données

    Un script sql est fournit pour créer la base de données.
    Pour l'accés à la base de données, il suffit de modifier le fichier configs/config.json avec les informations de votre base de données.
    Vous pouvez également y modifier le durée de validité des otps.
