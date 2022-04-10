<?php
/***********************************************
 *********   VECTORES ERRORES    ***********
 **********************************************/	

/***************************/
// Errores de la Impresora //
/***************************/
$StatusImpresoraError[0]   = "";
$StatusImpresoraError[1]   = "";
$StatusImpresoraError[2]   = "";
$StatusImpresoraError[3]   = "Error de impresora";
$StatusImpresoraDetalle[3] = "Se ha interrumpido la conexi�n entre el controlador fiscal y la impresora.";
$StatusImpresoraError[4]   = "Impresora offline";
$StatusImpresoraDetalle[4] = "La impresora no ha logrado comunicarse dentro del per�odo de tiempo establecido.";
$StatusImpresoraError[5]  = "Falta papel del diario";
$StatusImpresoraDetalle[5] = "El sensor de papel del diario ha detectado falta de papel.";
$StatusImpresoraError[6]   = "Falta papel de tickets";
$StatusImpresoraDetalle[6] = "El sensor de papel de tickets ha detectado falta de papel.";
$StatusImpresoraError[7]   = "Buffer de impresora lleno";
$StatusImpresoraDetalle[7] = "El controlador fiscal convierte los comandos enviados por un host en comandos fiscales, y los almacena en un buffer antes de enviarlos a la impresora fiscal. Cuando el buffer se aproxima a su capacidad m�xima, este bit se activa. Cualquier comando que se env�e cuando este bit est� en 1 no se ejecuta y debe ser reenviado
por el host.";
$StatusImpresoraError[8]   = "Buffer de impresora vac�o";
$StatusImpresoraDetalle[8] = "Este bit se activa cuando el buffer mencionado en el punto anterior se encuentra vac�o. Es una indicaci�n al host de que todos los comandos fueron enviados a la impresora fiscal.";
$StatusImpresoraError[9]   = "Tapa de impresora abierta.";
$StatusImpresoraDetalle[9] = "";
$StatusImpresoraError[15]    = "Caj�n de dinero cerrado o ausente.";
$StatusImpresoraDetalle[15] = "";
$StatusImpresoraError[16]      = "OR l�gico de los bits 2-5, 8 y 14.";
$StatusImpresoraDetalle[16] = "Este bit se encuentra en 1 siempre que alguno de los bits del 2 al 5, el bit 8 o el 14 se encuentre en 1.";

/***************************/
// Errores del Controlador //
/***************************/
$StatusFiscalError  [0] = "Error en chequeo de memoria fiscal.";
$StatusFiscalDetalle[0] = "Al encenderse la impresora se produjo un error en el checksum. La impresora no funcionar�.";
$StatusFiscalError  [1] = "Error en chequeo de memoria de trabajo";
$StatusFiscalDetalle[1] = "Al encenderse la impresora se produjo un error en el checksum. La impresora no funcionar�.";
$StatusFiscalError  [2] = "Carga de bater�a baja";
$StatusFiscalDetalle[2] = "La carga de la bater�a de respaldo de la memoria de trabajo se encuentra baja.";
$StatusFiscalError  [3] = "Comando desconocido";
$StatusFiscalDetalle[3] = "El comando recibido no fue reconocido.";
$StatusFiscalError  [4] = "Datos no v�lidos en un campo";
$StatusFiscalDetalle[4] = "Uno de los campos del comando recibido tiene datos no v�lidos por ejemplo, datos	no num�ricos en un campo num�rico).";
$StatusFiscalError  [5] = "Comando no v�lido para el estado fiscal actual";
$StatusFiscalDetalle[5] = "Se ha recibido un comando que no es v�lido en el estado actual del controlador (por ejemplo, abrir un recibo no-fiscal cuando se encuentra abierto un recibo fiscal).	Nota: cuando se ha producido un cambio no v�lido de c�digo de IVA, tanto el bit 4 como el 5 tendr�n valor 1.";
$StatusFiscalError  [6] = "Desborde del Total";
$StatusFiscalDetalle[6] = "El acumulador de una transacci�n, del total diario o del IVA se desbordar� a ra�z de	un comando recibido. El comando no es ejecutado. Este bit debe ser monitoreado por el host para emitir un aviso de error.";
$StatusFiscalError  [7] = "Memoria fiscal llena, bloqueada o dada de baja";
$StatusFiscalDetalle[7] = "En caso de que la memoria fiscal est� llena, bloqueada o dada de baja, no se	permite abrir un comprobante fiscal.";
$StatusFiscalError  [8] = "Memoria fiscal a punto de llenarse";
$StatusFiscalDetalle[8] = "La memoria fiscal tiene 30 o menos registros libres.	Este bit debe ser monitoreado por el host para emitir el correspondiente aviso.";
$StatusFiscalError  [9] = "Terminal fiscal certificada";
$StatusFiscalDetalle[9] = "Indica que la impresora ha sido inicializada.";
$StatusFiscalError  [10] = "Terminal fiscal fiscalizada";
$StatusFiscalDetalle[10] = "Indica que la impresora ha sido inicializada.";
$StatusFiscalError  [11] = "Error en ingreso de fecha";
$StatusFiscalDetalle[11] = "Se ha ingresado una fecha no v�lida. Para volver al bit a 0 debe ingresarse una fecha v�lida.";
$StatusFiscalError  [12] = "Documento fiscal abierto";
$StatusFiscalDetalle[12] = "Este bit se encuentra en 1 siempre que un documento fiscal se encuentra abierto.";
$StatusFiscalError  [13] = "Documento abierto";
$StatusFiscalDetalle[13] = "Este bit se encuentra en 1 siempre que un documento (fiscal, no fiscal o no fiscal homologado) se encuentra abierto.";
$StatusFiscalError  [15] = "OR l�gico de los bits 0 a 8.";
$StatusFiscalDetalle[15] = "Este bit se encuentra en 1 siempre que alguno de los bits mencionados se encuentre en 1.";				

/*******************************/
// Errores del Estado Auxiliar //
/*******************************/
$StatusAuxiliarError[1]  = "Memoria fiscal no inicializada.";
$StatusAuxiliarError[2]  = "No hay ning�n comprobante abierto.";
$StatusAuxiliarError[3]  = "Un comprobante fiscal se encuentra abierto. Venta habilitada.";
$StatusAuxiliarError[4]  = "Comprobante fiscal abierto. Se acaba de imprimir un texto fiscal.";
$StatusAuxiliarError[5]  = "Un comprobante no fiscal se encuentra abierto.";
$StatusAuxiliarError[6]  = "Comprobante fiscal abierto. Se realiz� al menos un pago.";
$StatusAuxiliarError[7]  =  "Comprobante fiscal abierto. Se sald� el monto.";
$StatusAuxiliarError[8]  = "Comprobante fiscal abierto. Se realiz� una percepci�n.";
$StatusAuxiliarError[9]  = "El controlador ha sido dado de baja.";
$StatusAuxiliarError[10] = "Comprobante fiscal abierto. Se realiz� un descuento / recargo general.";
$StatusAuxiliarError[11] = "Comp. fiscal abierto. Se realiz� una bonificaci�n / recargo / devoluci�n envases.";
$StatusAuxiliarError[13] = "Una nota de cr�dito se encuentra abierta. Cr�dito (venta) habilitado.";
$StatusAuxiliarError[14] = "Nota de cr�dito se encuentra abierta. Se realiz� una bonificaci�n / recargo / devoluci�n envases.";
$StatusAuxiliarError[15] = "Nota de cr�dito se encuentra abierta. Se realiz� un descuento / recargo general.";
$StatusAuxiliarError[16] = "Nota de cr�dito se encuentra abierta. Se realiz� una percepci�n.";
$StatusAuxiliarError[17] = "Nota de cr�dito se encuentra abierta. Se acaba de imprimir un texto fiscal.			";