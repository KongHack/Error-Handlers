# GCWorld Error Handlers

Just a simple set of error handlers you can use in your project.

## Example

Place this in your front controller after loading composer

	set_error_handler('\GCWorld\ErrorHandlers\ErrorHandlers::errorHandler');
	set_exception_handler('\GCWorld\ErrorHandlers\ErrorHandlers::exceptionHandler');
	register_shutdown_function('\GCWorld\ErrorHandlers\ErrorHandlers::shutdownHandler');

### Version
1.0.5

### Additional Information

* [GCWorld Public Gitlab](https://gitlab.konghack.com/groups/GCWorld)