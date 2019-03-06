# GCWorld Error Handlers

Just a simple set of error handlers you can use in your project.

## Example

Place this in your front controller after requiring your auto-loader

	set_error_handler('\GCWorld\ErrorHandlers\ErrorHandlers::errorHandler');
	set_exception_handler('\GCWorld\ErrorHandlers\ErrorHandlers::exceptionHandler');
	register_shutdown_function('\GCWorld\ErrorHandlers\ErrorHandlers::shutdownHandler');

### Version
1.2.1

### Additional Information

* [GCWorld Github Group](https://github.com/KongHack)