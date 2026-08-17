<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Renderer;

use RamElectronic\DataTableBundle\Application\ReadModel\Action;
use RamElectronic\DataTableBundle\Presentation\Twig\DataTable\Util\ValueReaderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ActionsCellRenderer
{
    public function __construct(
        private ValueReaderInterface $valueReader,
        private UrlGeneratorInterface $urlGenerator,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * Render actions cell for a table row.
     *
     * @param array<Action> $actions
     */
    public function render(array $actions, mixed $row): string
    {
        if ([] === $actions) {
            return '';
        }

        $id = $this->valueReader->read($row, 'id');

        $buttonsHtml = [];

        foreach ($actions as $action) {
            $buttonsHtml[] = $this->renderAction($action, $id);
        }

        return \sprintf(
            '<div class="flex items-center justify-end gap-1">%s</div>',
            implode('', $buttonsHtml)
        );
    }

    private function renderAction(Action $action, mixed $id): string
    {
        $url = $this->urlGenerator->generate($action->route, ['id' => $id]);

        return match ($action->type) {
            'edit' => $this->renderEditButton($url, $action->label),
            'delete' => $this->renderDeleteButton($url, $id, $action->label, $action->confirmMessage),
            'pdf' => $this->renderPdfButton($url),
            'view' => $this->renderViewButton($url, $action->label),
            default => $this->renderCustomButton($url, $action->label ?? $action->type),
        };
    }

    private function renderEditButton(string $url, ?string $label): string
    {
        $translatedLabel = $this->translator->trans($label ?? 'action.edit');

        return \sprintf(
            '<a href="%s" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">%s</a>',
            htmlspecialchars($url, \ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($translatedLabel, \ENT_QUOTES, 'UTF-8')
        );
    }

    private function renderDeleteButton(string $url, mixed $id, ?string $label, ?string $confirmMessage): string
    {
        $idStr = match (true) {
            \is_int($id) => (string) $id,
            \is_string($id) => $id,
            default => '',
        };
        $tokenValue = $this->csrfTokenManager->getToken('delete'.$idStr)->getValue();
        $translatedLabel = $this->translator->trans($label ?? 'action.delete');
        $translatedConfirm = $this->translator->trans($confirmMessage ?? 'confirm.delete');

        return \sprintf(
            '<form method="post" action="%s" class="inline" onsubmit="return confirm(\'%s\');">
                <input type="hidden" name="_token" value="%s">
                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-destructive text-destructive-foreground hover:bg-destructive/90 h-9 px-3">%s</button>
            </form>',
            htmlspecialchars($url, \ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($translatedConfirm, \ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($tokenValue, \ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($translatedLabel, \ENT_QUOTES, 'UTF-8')
        );
    }

    private function renderPdfButton(string $url): string
    {
        return \sprintf(
            '<a href="%s" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </a>',
            htmlspecialchars($url, \ENT_QUOTES, 'UTF-8')
        );
    }

    private function renderViewButton(string $url, ?string $label): string
    {
        $translatedLabel = $this->translator->trans($label ?? 'action.view');

        return \sprintf(
            '<a href="%s" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-3">%s</a>',
            htmlspecialchars($url, \ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($translatedLabel, \ENT_QUOTES, 'UTF-8')
        );
    }

    private function renderCustomButton(string $url, string $label): string
    {
        $translatedLabel = $this->translator->trans($label);

        return \sprintf(
            '<a href="%s" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">%s</a>',
            htmlspecialchars($url, \ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($translatedLabel, \ENT_QUOTES, 'UTF-8')
        );
    }
}
