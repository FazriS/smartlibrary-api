<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "SmartLibrary API",
    description: "Dokumentasi API SmartLibrary"
)]
#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Local Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Masukkan token JWT kamu di sini (tanpa kata 'Bearer ')"
)]
class OpenApi
{
}