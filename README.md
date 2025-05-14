# Lechuga Negra - FileManager AWS para Laravel

Este paquete de Laravel proporciona la gestión de archivos con servicio S3 de AWS, brindando los endpoints para la generación de rutas temporales que permitan la subida, descarga y eliminación de archivos de manera segura.

## Características Principales

* **Generar URL de Subida de archivo:** Solicitud para generar enlace temporal para subir un archivo.
* **Generar URL de Descarga de archivo:** Solicitud para generar enlace temporal para descargar un archivo.
* **Eliminar archivo:** Solicitud para la eliminación de archivo.
* **Servicio para generar URL temporal de subida de archivo:** Servicio usado por el endpoint para generar enlace temporal para subir un archivo.
* **Servicio para generar URL temporal de descarga de archivo:** Servicio usado por el endpoint para generar enlace temporal para descargar un archivo.
* **Servicio para eliminar archivo:** Servicio usado por el endpoint para la aliminación de un archivo.

## Instalación

1.  **Crear grupo de paquetes:**

    Crear la carpeta packages en la raíz del proyecto e ingresar a la carpeta:

    ```bash
    mkdir packages
    cd packages
    ```

    Crear el grupo de carpetas dentro de la carpeta creada, e ingresar a l carpeta:
    
    ```bash
    mkdir lechuganegra
    cd lechuganegra
    ```

2.  **Clonar el paquete:**

    Clonar el paquete en el grupo de carpetas creado y renombrarlo para que el Provider pueda registrarlo en la instalación

    ```bash
    git clone https://github.com/mtorres-lechuganegra/package-filemanager-aws.git filemanager-aws
    ```

3.  **Configurar composer del proyecto:**

    Dirígite a la raíz de tu proyecto, edita tu archivo `composer.json` y añade el paquete como repositorio:

    ```json
    {
        "repositories": [
            {
                "type": "path",
                "url": "packages/lechuganegra/filemanager-aws"
            }
        ]
    }
    ```
    también deberás añadir el namespace del paquete al autoloading de PSR-4:

    ```json
    {
        "autoload": {
            "psr-4": {
                "LechugaNegra\\AwsFileManager\\": "packages/lechuganegra/filemanager-aws/src/"
            }
        }
    }
    ```

4.  **Ejecutar composer require:**

    Después de editar tu archivo, abre tu terminal y ejecuta el siguiente comando para agregar el paquete a las dependencias de tu proyecto:

    ```bash
    composer require lechuganegra/filemanager-aws:@dev
    ```

    Este comando descargará el paquete y actualizará tu archivo `composer.json`.

5.  **Agregar variables de entorno:**

    Agregar las variables de eneotnor de FILASYSTEM y credenciales AWS en .env:

    ```nginx
    FILESYSTEM_DISK=s3

    AWS_ACCESS_KEY_ID=your-access-key
    AWS_SECRET_ACCESS_KEY=your-secret-key
    AWS_DEFAULT_REGION=us-east-1
    AWS_BUCKET=your-bucket-name
    AWS_URL=https://your-bucket.s3.amazonaws.com
    AWS_VISIBILITY=private
    AWS_TTL_URL_UPLOAD=1
    AWS_TTL_URL_DOWNLOAD=1
    ```

    La variable `AWS_VISIBILITY` no es necesaria si el Bucket de AWS tiene habilitado el ACL.

6.  **Verificar configuración s3:**

    Verificar que el archivo `config/filesystems.php` en `s3` tenga los siguientes valores:

    ```php
    'disk' => [
        's3' => [
            ...
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'visibility' => env('AWS_VISIBILITY', 'private'),
            'ttl' => [
                'url_upload' => env('AWS_TTL_URL_UPLOAD', '1'),
                'url_download' => env('AWS_TTL_URL_DOWNLOAD', '1')
            ]
            ...
        ]
    ],
    ```

7.  **Limpiar la caché:**

    Limpia la caché de configuración y rutas para asegurar que los cambios se apliquen correctamente:

    ```bash
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan route:cache
    ```
    
8.  **Regenerar clases:**

    Regenerar las clases con el cargador automático "autoload"

    ```bash
    composer dump-autoload
    ```

## Uso

### Endpoints del Servicio

Son 3 endpoints destinados a la generación de URL de subida, de descarga y a la eliminación de archivo, su uso está sujeto a la configuración del bucket.

PDT: No usar parámetro `acl` o `folder` en el request de generación de URL de subida de archivo si no cuenta con los permisos otorgados en la configuración del bucket.