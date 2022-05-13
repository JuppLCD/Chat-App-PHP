# Chat App PHP con Sockets

Esta App fue hecha con la finalidad de aprender y comprender php. Es php vanilla + 1 libreria (sockets - Ratchet) y con mysql como DB, **esta basada en un video de youtube**, del cual procedo a dejar el [link aqui](https://www.youtube.com/watch?v=VnvzxGWiK54). [Donde conseguir el codigo de CodingNepal](https://www.codingnepalweb.com/chat-web-application-using-php/). [Su cuenta de Youtube](https://www.youtube.com/c/codingnepal).

El codigo no es igual al del video, ya que fue refactorizado, principalmente al añadir clases y hacer que la app funcione mediante sockets utilizando la libreria Ratchet PHP.

### Uso

##### Configurar los datos de DB en ./php/db/config.sjon

```
{
	"conexion": {
		"server": localhost,
		"user": User-Name ,
		"password": User-Password,
		"database": "chatapp",
		"port": "3306"
	}
}
```

##### Comando para que corra el servior con socket

```
composer serve
```
