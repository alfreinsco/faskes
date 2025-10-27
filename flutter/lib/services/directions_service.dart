import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:google_polyline_algorithm/google_polyline_algorithm.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';

class DirectionsService {
  static const String _baseUrl =
      'https://maps.googleapis.com/maps/api/directions/json';
  static const String _apiKey =
      'AIzaSyA2_TmND7BFRzB_RBkz-qivwikAxjtTE9g'; // Gunakan API key yang sama

  static Future<DirectionsResult?> getDirections({
    required double originLat,
    required double originLng,
    required double destinationLat,
    required double destinationLng,
  }) async {
    try {
      final url = Uri.parse(_baseUrl).replace(
        queryParameters: {
          'origin': '$originLat,$originLng',
          'destination': '$destinationLat,$destinationLng',
          'key': _apiKey,
          'mode': 'driving', // driving, walking, bicycling, transit
          'avoid': 'tolls', // hindari tol jika memungkinkan
          'alternatives': 'false', // hanya satu rute
          'language': 'id', // bahasa Indonesia
          'region': 'id', // region Indonesia
        },
      );

      print(
        'Requesting directions from: $originLat,$originLng to: $destinationLat,$destinationLng',
      );
      print('API URL: $url');

      final response = await http
          .get(
            url,
            headers: {
              'Accept': 'application/json',
              'User-Agent': 'FaskesApp/1.0',
            },
          )
          .timeout(
            const Duration(seconds: 30),
            onTimeout: () {
              throw Exception('Request timeout');
            },
          );

      print('Response status: ${response.statusCode}');
      print('Response body: ${response.body}');

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        print('Parsed data: $data');

        if (data['status'] == 'OK' && data['routes'].isNotEmpty) {
          final route = data['routes'][0];
          final leg = route['legs'][0];

          // Decode polyline
          final polylinePoints = decodePolyline(
            route['overview_polyline']['points'],
          );

          // Convert to LatLng list
          final List<LatLng> points = polylinePoints
              .map((point) => LatLng(point[0].toDouble(), point[1].toDouble()))
              .toList();

          return DirectionsResult(
            points: points,
            distance: leg['distance']['text'],
            duration: leg['duration']['text'],
            instructions: _extractInstructions(route['legs']),
          );
        } else {
          print(
            'Directions API error: ${data['status']} - ${data['error_message'] ?? 'Unknown error'}',
          );
          print('Full error response: $data');
          return null;
        }
      } else {
        print('HTTP error: ${response.statusCode}');
        print('Response body: ${response.body}');
        return null;
      }
    } catch (e) {
      print('Error getting directions: $e');
      print('Stack trace: ${StackTrace.current}');
      return null;
    }
  }

  static List<String> _extractInstructions(List<dynamic> legs) {
    List<String> instructions = [];

    for (var leg in legs) {
      if (leg['steps'] != null) {
        for (var step in leg['steps']) {
          if (step['html_instructions'] != null) {
            // Remove HTML tags
            String instruction = step['html_instructions']
                .replaceAll(RegExp(r'<[^>]*>'), '')
                .replaceAll('&nbsp;', ' ')
                .trim();
            instructions.add(instruction);
          }
        }
      }
    }

    return instructions;
  }
}

class DirectionsResult {
  final List<LatLng> points;
  final String distance;
  final String duration;
  final List<String> instructions;

  DirectionsResult({
    required this.points,
    required this.distance,
    required this.duration,
    required this.instructions,
  });
}
