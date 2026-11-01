<?php

namespace App\Mail;

use App\Models\DesignAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorPrintDispatchMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DesignAsset $asset,
        public ?string $notes = null,
        public ?string $senderName = null,
        public mixed $allAssets = null
    ) {}

    public function envelope(): Envelope
    {
        $order = $this->asset->designJob->order;
        $orderNumber = $order->order_number ?? 'ORDER';
        $title = $this->asset->designJob->title ?? 'Berkas Cetak';
        $count = $this->allAssets ? count($this->allAssets) : 1;
        $fileInfo = $count > 1 ? "({$count} Berkas)" : "File Siap Cetak";

        return new Envelope(
            subject: "[ORDER CETAK #{$orderNumber}] {$fileInfo}: {$title} - Gemini Art",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-dispatch',
            with: [
                'asset' => $this->asset,
                'allAssets' => $this->allAssets,
                'job' => $this->asset->designJob,
                'order' => $this->asset->designJob->order,
                'branch' => $this->asset->designJob->order->branch,
                'notes' => $this->notes,
                'senderName' => $this->senderName,
                'shareUrl' => $this->asset->share_url,
                'downloadUrl' => route('vendor.share.download', $this->asset->share_token),
                'previewUrl' => route('vendor.share.preview', $this->asset->share_token),
            ],
        );
    }
}

