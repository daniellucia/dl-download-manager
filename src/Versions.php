<?php

namespace DL\DownloadManager;

class Versions
{

    private $post;
    private $versions = [];

    /**
     * Constructor
     * @param mixed $post
     * @author Daniel Lúcia
     */
    public function __construct($post)
    {
        if (is_numeric($post)) {
            $post = get_post((int)$post);
        }

        if (!($post instanceof \WP_Post)) {
            throw new \InvalidArgumentException('The argument must be a WP_Post object or a valid post ID.');
        }

        if ($post->post_type !== Constant::KEY) {
            throw new \InvalidArgumentException(sprintf('The post must be of type %s', Constant::KEY));
        }

        $this->post = $post;
        $this->versions = get_post_meta($this->post->ID, '_dl_versions', true);
        if (!is_array($this->versions)) {
            $this->versions = [];
        }

        $this->orderVersions();
    }

    /**
     * Ordenamos las versiones por versión (de mayor a menor)
     * @return void
     * @author Daniel Lúcia
     */
    public function orderVersions()
    {
        $versions = [];
        foreach ($this->versions as $version) {
            $versions[$version['version']] = $version;
        }

        krsort($versions);
        $this->versions = $versions;
    }

    /**
     * Obtenemos las versions de una descarga
     * @return array
     * @author Daniel Lúcia
     */
    public function get(): array
    {
        return $this->versions;
    }

    /**
     * Creamos una nueva version
     * @param string $path
     * @param string $version
     * @param string $file_url
     * @return array{file: string, token: string, version: string}
     * @author Daniel Lúcia
     */
    public function create(string $attachment_id, string $version, string $file_url): array
    {

        $token = wp_generate_password(20, false, false);
        $version = [
            'version' => $version,
            'attachment_id'    => $attachment_id,
            'file_url' => $file_url,
            'token'   => $token,
            'downloads' => 0,
            'date_created' => current_time('mysql')
        ];

        return $version;
    }

    /**
     * Guardamos una version
     * @param array{file: string, token: string, version: string} $version
     * @return void
     * @author Daniel Lúcia
     */
    public function deleteByToken(string $token): void
    {
        foreach ($this->versions as $index => $version) {
            if ($version['token'] === $token) {
                unset($this->versions[$index]);
                break;
            }
        }

        // Reindexar el array para evitar huecos en las claves
        $this->versions = array_values($this->versions);

        update_post_meta($this->post->ID, '_dl_versions', $this->versions);
    }

    /**
     * Obtenemos la ruta del fichero asociado a un token
     * @param string $token
     * @return mixed|null
     * @author Daniel Lúcia
     */
    public function getFileByToken(string $token): ?string
    {

        foreach ($this->versions as $version) {

            if ($version['token'] === $token) {
                $attachment_id = $version['attachment_id'];

                return get_attached_file($attachment_id);
            }
        }

        return null;
    }

    /**
     * Incrementa el contador de descargas de una versión
     * @param string $token
     * @return void
     * @author Daniel Lúcia
     */
    public function incrementDownloads(string $token): void
    {
        foreach ($this->versions as &$version) {
            if ($version['token'] === $token) {
                if (!isset($version['downloads'])) {
                    $version['downloads'] = 0;
                }
                $version['downloads']++;
                break;
            }
        }
        unset($version);

        update_post_meta($this->post->ID, '_dl_versions', $this->versions);
    }
}
