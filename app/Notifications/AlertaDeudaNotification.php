<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Cuota;

class AlertaDeudaNotification extends Notification
{
use Queueable;

    public $cuota;
    public $tipo; // 'warning' (por vencer) o 'danger' (vencido)

    /**
     * Recibimos la cuota y el tipo de alerta.
     */
    public function __construct(Cuota $cuota, $tipo)
    {
        $this->cuota = $cuota;
        $this->tipo = $tipo;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Guardar en la campanita
    }

    public function toArray(object $notifiable): array
    {
        // Configuramos el mensaje y el icono según el tipo
        if ($this->tipo == 'warning') {
            $titulo = '⏳ Vence Pronto';
            $mensaje = "La cuota '{$this->cuota->descripcion}' de {$this->cuota->venta->cliente->nombre_completo} vence en 3 días.";
            $icon = 'fa-clock';
            $color = 'warning'; // Naranja
        } else {
            $titulo = '🚨 ¡Deuda Vencida!';
            $mensaje = "La cuota '{$this->cuota->descripcion}' de {$this->cuota->venta->cliente->nombre_completo} ha vencido hoy.";
            $icon = 'fa-exclamation-triangle';
            $color = 'danger'; // Rojo
        }

        return [
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'link' => route('cobranzas.show', $this->cuota->venta_id), // Link al detalle del alumno
            'icon' => $icon,
            'color' => $color
        ];
    }
}
