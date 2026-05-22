<?php
declare(strict_types=1);

namespace GDW\Core\Block\Adminhtml\System\Core;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ConsultingServices extends Fieldset
{
    public function render(AbstractElement $element): string
    {
        return $this->getServicesHtml();
    }

    public function getServicesHtml(): string
    {
        $services = [
            [
                'title' => 'Desarrollo Personalizado',
                'description' => 'Creación de módulos, extensiones y customizaciones según tus necesidades específicas.',
                'icon' => '⚙️',
                'items' => [
                    'Módulos customizados',
                    'Integraciones con terceros',
                    'APIs y webservices',
                    'Automatizaciones',
                ],
            ],
            [
                'title' => 'Optimización & Performance',
                'description' => 'Mejora de velocidad, escalabilidad y eficiencia de tu tienda Magento.',
                'icon' => '⚡',
                'items' => [
                    'Auditoría de performance',
                    'Optimización de bases de datos',
                    'Caché y CDN',
                    'SEO técnico',
                ],
            ],
            [
                'title' => 'Soporte & Mantenimiento',
                'description' => 'Asistencia técnica, bugfixes, parches de seguridad y monitoreo continuo.',
                'icon' => '🛡️',
                'items' => [
                    'Soporte técnico 24/7',
                    'Monitoreo proactivo',
                    'Parches de seguridad',
                    'Mantenimiento preventivo',
                ],
            ],
            [
                'title' => 'Consultoría Estratégica',
                'description' => 'Asesoramiento en arquitectura, mejores prácticas y roadmap tecnológico.',
                'icon' => '📋',
                'items' => [
                    'Arquitectura de sistemas',
                    'Plan de crecimiento',
                    'Mejores prácticas Magento',
                    'Capacitación de equipo',
                ],
            ],
        ];

        $html = $this->renderHeader();
        foreach ($services as $service) {
            $html .= $this->renderServiceCard($service);
        }
        $html .= $this->renderContactSection();
        $html .= $this->renderFooter();

        return $html;
    }

    public function renderHeader(): string
    {
        return implode('', [
            '<div style="background: #373330; padding: 30px; border-radius: 8px; margin-bottom: 20px; color: white;">',
            '<h2 style="margin: 0 0 10px 0; font-size: 28px; color: white;">Servicios de Consultoría & Desarrollo</h2>',
            '<p style="margin: 0; opacity: 0.9;">Potencia tu tienda Magento con soluciones profesionales</p>',
            '</div>',
        ]);
    }

    public function renderServiceCard(array $service): string
    {
        $itemsList = '';
        foreach ($service['items'] as $item) {
            $itemsList .= "<li style='margin: 5px 0; color: #555;'>✓ {$item}</li>";
        }

        return implode('', [
            '<div style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">',
            '<div style="display: flex; align-items: flex-start;">',
            '<div style="font-size: 32px; margin-right: 15px;">' . $service['icon'] . '</div>',
            '<div style="flex: 1;">',
            '<h3 style="margin: 0 0 8px 0; color: #667eea; font-size: 18px;">' . $service['title'] . '</h3>',
            '<p style="margin: 0 0 12px 0; color: #666; font-size: 13px;">' . $service['description'] . '</p>',
            '<ul style="margin: 0; padding-left: 20px; font-size: 12px;">',
            $itemsList,
            '</ul>',
            '</div>',
            '</div>',
            '</div>',
        ]);
    }

    public function renderContactSection(): string
    {
        return implode('', [
            '<div style="background: #f8f9fa; border: 2px solid #667eea; border-radius: 8px; padding: 25px; margin-top: 30px; text-align: center;">',
            '<h3 style="margin: 0 0 15px 0; color: #333; font-size: 20px;">¿Quieres contratar nuestros servicios?</h3>',
            '<p style="margin: 0 0 20px 0; color: #666; font-size: 13px;">',
            'Contacta con nosotros para discutir tu proyecto y obtener una cotización personalizada.',
            '</p>',
            '<div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">',
            '<a href="mailto:jcruz@gdw.mx" style="background: #667eea; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block;">',
            '📧 Email: jcruz@gdw.mx',
            '</a>',
            '<a href="https://gdw.mx" target="_blank" style="background: #764ba2; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block;">',
            '🌐 Visita gdw.mx',
            '</a>',
            '<a href="https://www.linkedin.com/in/jose-cruz-chavez" target="_blank" style="background: #0077b5; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block;">',
            '💼 LinkedIn',
            '</a>',
            '</div>',
            '</div>',
        ]);
    }

    public function renderFooter(): string
    {
        return implode('', [
            '<div style="margin-top: 30px; padding: 15px; background: #fafafa; border-left: 4px solid #667eea; border-radius: 4px; font-size: 12px; color: #666;">',
            '<strong>Nota:</strong> Todos nuestros servicios incluyen garantía de calidad, cumplimiento de plazos y ',
            'actualización continua con las últimas versiones de Magento.',
            '</div>',
        ]);
    }
}
