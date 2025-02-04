<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

/**
 * RajaOngkir Integration Trait
 *
 * This trait provides methods to interact with the RajaOngkir API for shipping-related
 * operations in Indonesia, including province and city lookup, shipping cost calculation,
 * and waybill tracking.
 *
 * @package App\Traits
 */
trait RajaOngkirTrait
{
    /**
     * Base URL for RajaOngkir API
     */
    private const API_BASE_URL = 'https://pro.rajaongkir.com/api';

    /**
     * Retrieve all provinces from RajaOngkir
     *
     * @return array<string, mixed> Array containing province data
     * @throws RequestException When the API request fails
     */
    public function getProvince(): array
    {
        $response = $this->_makeRequest('GET', '/province');
        return $response['rajaongkir']['results'];
    }

    /**
     * Retrieve cities for a specific province
     *
     * @param int $provinceId The ID of the province
     * @return array<string, mixed> Array containing city data
     * @throws RequestException When the API request fails
     * @throws InvalidArgumentException When province ID is invalid
     */
    public function getCity(int $provinceId): array
    {
        if ($provinceId <= 0) throw new InvalidArgumentException('Province ID must be a positive integer');

        $response = $this->_makeRequest('GET', '/city', [
            'province' => $provinceId
        ]);
        return $response['rajaongkir']['results'];
    }

    /**
     * Retrieve districts for a specific city
     *
     * @param int $cityId The ID of the city
     * @return array<string, mixed> Array containing district data
     * @throws RequestException When the API request fails
     * @throws InvalidArgumentException When city ID is invalid
     */
    public function getDistrict(int $cityId): array
    {
        if ($cityId <= 0) throw new InvalidArgumentException('City ID must be a positive integer');

        $response = $this->_makeRequest('GET', '/subdistrict', [
            'city' => $cityId
        ]);
        return $response['rajaongkir']['results'];
    }

    /**
     * Retrieve province information by ID
     *
     * @param int $id The ID of the province
     * @return array<string, mixed> Array containing province data
     * @throws RequestException When the API request fails
     * @throws InvalidArgumentException When province ID is invalid
     */
    public function getProvinceById(int $id): array
    {
        if ($id <= 0) throw new InvalidArgumentException('Province ID must be a positive integer');

        $response = $this->_makeRequest('GET', '/province', ['id' => $id]);
        return $response['rajaongkir'];
    }

    /**
     * Retrieve city information by ID
     *
     * @param int $id The ID of the city
     * @return array<string, mixed> Array containing city data
     * @throws RequestException When the API request fails
     * @throws InvalidArgumentException When city ID is invalid
     */
    public function getCityById(int $id): array
    {
        if ($id <= 0) throw new InvalidArgumentException('City ID must be a positive integer');

        $response = $this->_makeRequest('GET', '/city', ['id' => $id]);
        return $response['rajaongkir'];
    }

    /**
     * Retrieve district information by ID
     *
     * @param int $id The ID of the district
     * @return array<string, mixed> Array containing district data
     * @throws RequestException When the API request fails
     * @throws InvalidArgumentException When district ID is invalid
     */
    public function getDistrictById(int $id): array
    {
        if ($id <= 0) throw new InvalidArgumentException('District ID must be a positive integer');

        $response = $this->_makeRequest('GET', '/subdistrict', ['id' => $id]);
        return $response['rajaongkir'];
    }

    /**
     * Calculate shipping cost between locations
     *
     * @param int $origin Origin location ID
     * @param string $originType Type of origin location ('city' or 'subdistrict')
     * @param int $destination Destination location ID
     * @param string $destinationType Type of destination location ('city' or 'subdistrict')
     * @param int $weight Package weight in grams
     * @param string $courier Courier service code
     * @return array<string, mixed> Shipping cost calculation data
     * @throws RequestException When the API request fails
     * @throws InvalidArgumentException When input parameters are invalid
     */
    public function checkCost(
        int $origin,
        string $originType,
        int $destination,
        string $destinationType,
        int $weight,
        string $courier
    ): array {
        if ($weight <= 0) {
            throw new InvalidArgumentException('Weight must be greater than 0 grams');
        }

        $validTypes = ['city', 'subdistrict'];
        if (!in_array($originType, $validTypes) || !in_array($destinationType, $validTypes)) {
            throw new InvalidArgumentException('Invalid location type. Must be either "city" or "subdistrict"');
        }

        $response = $this->_makeRequest('POST', '/cost', [
            'origin'          => $origin,
            'originType'      => $originType,
            'destination'     => $destination,
            'destinationType' => $destinationType,
            'weight'          => $weight,
            'courier'         => $courier,
        ]);

        return $response['rajaongkir']['results'][0];
    }

    /**
     * Track shipment using waybill number
     *
     * @param string $waybill Waybill tracking number
     * @param string $courier Courier service code
     * @return array<string, mixed> Array containing tracking data
     * @throws RequestException When the API request fails
     * @throws InvalidArgumentException When waybill or courier is empty
     */
    public function getWaybill(string $waybill, string $courier): array
    {
        if (empty($waybill) || empty($courier)) throw new InvalidArgumentException('Waybill number and courier code are required');

        $response = $this->_makeRequest('POST', '/waybill', [
            'waybill' => $waybill,
            'courier' => $courier,
        ]);

        return $response['rajaongkir'];
    }

    /**
     * Make an HTTP request to the RajaOngkir API
     *
     * @param string $method HTTP method (GET or POST)
     * @param string $endpoint API endpoint
     * @param array<string, mixed> $params Request parameters
     * @return array<string, mixed> Decoded API response
     * @throws RequestException When the API request fails
     */
    private function _makeRequest(string $method, string $endpoint, array $params = []): array
    {
        $url = self::API_BASE_URL . $endpoint;

        $headers = [
            'key'          => config('app.raja_ongkir'),
            'content-type' => 'application/x-www-form-urlencoded',
        ];

        $request = Http::withHeaders($headers);

        $response = match (strtoupper($method)) {
            'GET'   => $request->get($url, $params),
            'POST'  => $request->asForm()->post($url, $params),
            default => throw new InvalidArgumentException('Unsupported HTTP method'),
        };

        return $response->throw()->json();
    }
}