<?php
/**
 * @author Jocelyn Flament
 * @since 19/05/2025
 */

namespace App\Constant;

class Media
{
    /**
     * nom du sous-dossier du répertoire public/ , utilisé pour le stockage des images du site
     * Si UPLOAD_DIR = 'uploads' alors les images doivent être dans le dossier 'public/uploads'
     * Si vous modifiez cette valeur, n'oubliez pas de renommer le répertoire avec le nouveau nom
     */
    public const UPLOAD_DIR = 'uploads';
}