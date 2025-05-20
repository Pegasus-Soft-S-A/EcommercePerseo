<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class ValidacionIdentificacionEcuatoriana implements Rule
{
    protected $message = '';

    public function passes($attribute, $value)
    {
        $value = trim($value);
        $longitud = strlen($value);

        // Validación básica de longitud
        if ($longitud != 10 && $longitud != 13) {
            $this->message = 'La identificación debe tener 10 dígitos para cédula o 13 para RUC.';
            return false;
        }

        // Validación de solo números
        if (!ctype_digit($value)) {
            $this->message = 'La identificación debe contener solo números.';
            return false;
        }

        // Verificar si todos los dígitos son iguales
        if ($this->todosDigitosIguales($value)) {
            $this->message = 'La identificación no puede tener todos los dígitos iguales.';
            return false;
        }

        // Obtener código de provincia
        $codigo_provincia = (int)substr($value, 0, 2);

        // Validar código de provincia (1-24 o 30)
        if ($codigo_provincia < 1 || ($codigo_provincia > 24 && $codigo_provincia != 30)) {
            $this->message = 'El código de provincia no es válido.';
            return false;
        }


        // Validar según longitud
        $esValido = false;
        if ($longitud === 10) {
            $esValido = $this->validarCedula($value);
        } elseif ($longitud === 13) {
            $esValido = $this->validarRUC($value);
        } else {
            $this->message = 'La longitud de la identificación no es válida.';
            return false;
        }

        // Si no pasa la validación algorítmica, retornar falso
        if (!$esValido) {
            return false;
        }

        // Verificar si la identificación ya existe en la base de datos
        $identificacion = $value;

        // Siempre verificamos la identificación exacta ingresada
        // Si es cédula (10 dígitos), verificamos también con '001' al final
        // Si es RUC (13 dígitos), verificamos también los primeros 10 dígitos (cédula)
        $identificacionesABuscar = [$identificacion];

        if (strlen($identificacion) == 10) {
            // Si es cédula, agregar posible RUC
            $identificacionesABuscar[] = $identificacion . '001';
        } elseif (strlen($identificacion) == 13) {
            // Si es RUC, agregar posible cédula
            $identificacionesABuscar[] = substr($identificacion, 0, 10);
        }

        $existeIdentificacion = User::whereIn('identificacion', $identificacionesABuscar)->exists();

        if ($existeIdentificacion) {
            $this->message = 'La identificación ya se encuentra registrada.';
            return false;
        }

        return true;
    }

    /**
     * Verifica si todos los dígitos de la identificación son iguales
     * 
     * @param string $valor Número de identificación
     * @return bool Verdadero si todos los dígitos son iguales
     */
    protected function todosDigitosIguales($valor)
    {
        $primerDigito = $valor[0];
        for ($i = 1; $i < strlen($valor); $i++) {
            if ($valor[$i] !== $primerDigito) {
                return false;
            }
        }
        return true;
    }

    protected function validarCedula($cedula)
    {
        $total = 0;
        $longcheck = 9; // Longitud a verificar (sin el último dígito)

        // Algoritmo de validación de cédula
        for ($i = 0; $i < $longcheck; $i++) {
            $digito = (int)substr($cedula, $i, 1);

            if ($i % 2 === 0) {
                $aux = $digito * 2;
                if ($aux > 9) $aux -= 9;
                $total += $aux;
            } else {
                $total += $digito;
            }
        }

        $verificador = $total % 10 ? 10 - ($total % 10) : 0;
        $ultimo_digito = (int)substr($cedula, 9, 1);

        if ($verificador !== $ultimo_digito) {
            $this->message = 'La Cédula no es válida.';
            return false;
        }

        return true;
    }

    protected function validarRUC($ruc)
    {
        // Validación RUC de persona natural
        if ($this->validarRUCPersonaNatural($ruc)) {
            return true;
        }

        // Validación RUC de sociedad privada
        if ($this->validarRUCSociedadPrivada($ruc)) {
            return true;
        }

        // Validación RUC de sociedad pública
        if ($this->validarRUCSociedadPublica($ruc)) {
            return true;
        }

        $this->message = 'El RUC no es válido.';
        return false;
    }

    protected function validarRUCPersonaNatural($ruc)
    {
        // Verificar que los 3 últimos dígitos sean 001
        if (substr($ruc, 10, 3) !== '001') {
            return false;
        }

        // Aplicar el mismo algoritmo de la cédula a los primeros 9 dígitos
        $cedula = substr($ruc, 0, 10);
        return $this->validarCedula($cedula);
    }

    protected function validarRUCSociedadPrivada($ruc)
    {
        // Verificar que los 3 últimos dígitos sean 001
        if (substr($ruc, 10, 3) !== '001') {
            return false;
        }

        $total = 0;
        $coeficientes = [4, 3, 2, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 9; $i++) {
            $total += (int)substr($ruc, $i, 1) * $coeficientes[$i];
        }

        $verificador = 11 - ($total % 11);
        if ($verificador == 11) {
            $verificador = 0;
        }

        if ($verificador == (int)substr($ruc, 9, 1)) {
            return true;
        }

        return false;
    }

    protected function validarRUCSociedadPublica($ruc)
    {
        // Verificar que los 4 últimos dígitos sean 0001
        if (substr($ruc, 9, 4) !== '0001') {
            return false;
        }

        $total = 0;
        $coeficientes = [3, 2, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 8; $i++) {
            $total += (int)substr($ruc, $i, 1) * $coeficientes[$i];
        }

        $verificador = 11 - ($total % 11);
        if ($verificador == 11) {
            $verificador = 0;
        }

        if ($verificador == (int)substr($ruc, 8, 1)) {
            return true;
        }

        return false;
    }

    public function message()
    {
        return $this->message ?: 'La identificación no cumple con la validación requerida.';
    }
}
