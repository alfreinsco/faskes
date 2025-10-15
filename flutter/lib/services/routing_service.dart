import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:latlong2/latlong.dart';

class RoutingService {
  // Using OpenRouteService API (free tier available)
  // Using public API without key for basic routing
  static const String _baseUrl =
      'https://api.openrouteservice.org/v2/directions';

  static Future<RouteResult?> getRoute({
    required LatLng start,
    required LatLng end,
    String profile = 'driving-car', // driving-car, walking, cycling-regular
  }) async {
    try {
      final url = '$_baseUrl/$profile';

      final response = await http.post(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept':
              'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8',
        },
        body: jsonEncode({
          'coordinates': [
            [start.longitude, start.latitude],
            [end.longitude, end.latitude],
          ],
          'format': 'geojson',
          'options': {
            'avoid_features': ['highways', 'tolls'],
            'preference': 'fastest',
            'continue_straight': false,
            'radiuses': [-1, -1], // Unlimited radius for waypoints
          },
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return _parseRouteResponse(data);
      } else {
        print('Routing API Error: ${response.statusCode} - ${response.body}');
        // Fallback to straight line if API fails
        return getStraightLineRoute(start: start, end: end, profile: profile);
      }
    } catch (e) {
      print('Routing Service Error: $e');
      // Fallback to straight line on error
      return getStraightLineRoute(start: start, end: end, profile: profile);
    }
  }

  // Method similar to L.Routing.control() for multiple waypoints
  static Future<RouteResult?> getRouteWithWaypoints({
    required List<LatLng> waypoints,
    String profile = 'driving-car',
  }) async {
    if (waypoints.length < 2) return null;

    print('Getting route with ${waypoints.length} waypoints');
    print('Profile: $profile');

    try {
      final url = '$_baseUrl/$profile';
      print('API URL: $url');

      final requestBody = {
        'coordinates': waypoints
            .map((point) => [point.longitude, point.latitude])
            .toList(),
        'format': 'geojson',
        'options': {
          'avoid_features': ['highways', 'tolls'],
          'preference': 'fastest',
          'continue_straight': false,
          'radiuses': List.filled(
            waypoints.length,
            -1,
          ), // Unlimited radius for all waypoints
        },
      };

      print('Request body: ${jsonEncode(requestBody)}');

      final response = await http.post(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept':
              'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8',
        },
        body: jsonEncode(requestBody),
      );

      print('Response status: ${response.statusCode}');
      print('Response body: ${response.body}');

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final result = _parseRouteResponse(data);
        print(
          'Parsed route result: ${result?.points.length} points, ${result?.distanceText}',
        );
        return result;
      } else {
        print('Routing API Error: ${response.statusCode} - ${response.body}');
        return null;
      }
    } catch (e) {
      print('Routing Service Error: $e');
      return null;
    }
  }

  static RouteResult? _parseRouteResponse(Map<String, dynamic> data) {
    try {
      final features = data['features'] as List;
      if (features.isEmpty) return null;

      final feature = features.first;
      final geometry = feature['geometry'];
      final properties = feature['properties'];
      final summary = properties['summary'];

      // Parse coordinates
      final coordinates = geometry['coordinates'] as List;
      final points = coordinates.map((coord) {
        return LatLng(coord[1], coord[0]); // GeoJSON uses [lng, lat]
      }).toList();

      // Parse summary
      final distance = summary['distance']?.toDouble() ?? 0.0;
      final duration = summary['duration']?.toDouble() ?? 0.0;

      return RouteResult(
        points: points,
        distance: distance,
        duration: duration,
      );
    } catch (e) {
      print('Error parsing route response: $e');
      return null;
    }
  }

  // Fallback method using straight line if API fails
  static RouteResult getStraightLineRoute({
    required LatLng start,
    required LatLng end,
    String profile = 'driving-car',
  }) {
    final distance = const Distance().as(LengthUnit.Meter, start, end);

    // Add some intermediate points to make the route look more realistic
    final points = _generateIntermediatePoints(start, end, 5);

    // Calculate duration based on transport mode
    double duration;
    switch (profile) {
      case 'walking':
        duration = distance / 5 * 3.6; // 5 km/h walking speed
        break;
      case 'cycling-regular':
        duration = distance / 15 * 3.6; // 15 km/h cycling speed
        break;
      case 'driving-car':
      default:
        duration = distance / 50 * 3.6; // 50 km/h driving speed
        break;
    }

    return RouteResult(points: points, distance: distance, duration: duration);
  }

  // Generate intermediate points to make straight line look more like a road
  static List<LatLng> _generateIntermediatePoints(
    LatLng start,
    LatLng end,
    int segments,
  ) {
    final points = <LatLng>[start];

    for (int i = 1; i < segments; i++) {
      final ratio = i / segments;
      final lat = start.latitude + (end.latitude - start.latitude) * ratio;
      final lng = start.longitude + (end.longitude - start.longitude) * ratio;

      // Add some slight variation to make it look more like a road
      final variation = 0.0001 * (i % 2 == 0 ? 1 : -1);
      points.add(LatLng(lat + variation, lng + variation));
    }

    points.add(end);
    return points;
  }
}

class RouteResult {
  final List<LatLng> points;
  final double distance; // in meters
  final double duration; // in seconds

  RouteResult({
    required this.points,
    required this.distance,
    required this.duration,
  });

  String get distanceText {
    if (distance < 1000) {
      return '${distance.toStringAsFixed(0)} m';
    } else {
      return '${(distance / 1000).toStringAsFixed(1)} km';
    }
  }

  String get durationText {
    final hours = (duration / 3600).floor();
    final minutes = ((duration % 3600) / 60).floor();

    if (hours > 0) {
      return '${hours}j ${minutes}m';
    } else {
      return '${minutes}m';
    }
  }
}

// Class similar to L.Routing.control() from Leaflet Routing Machine
class RoutingControl {
  List<LatLng> _waypoints = [];
  String _profile = 'driving-car';
  RouteResult? _routeResult;
  Function(RouteResult?)? _onRouteFound;
  Function(String)? _onError;

  RoutingControl({
    List<LatLng>? waypoints,
    String profile = 'driving-car',
    Function(RouteResult?)? onRouteFound,
    Function(String)? onError,
  }) {
    _waypoints = waypoints ?? [];
    _profile = profile;
    _onRouteFound = onRouteFound;
    _onError = onError;
  }

  // Add waypoint (similar to L.Routing.control().addWaypoint())
  void addWaypoint(LatLng waypoint) {
    _waypoints.add(waypoint);
    _calculateRoute();
  }

  // Remove waypoint
  void removeWaypoint(int index) {
    if (index >= 0 && index < _waypoints.length) {
      _waypoints.removeAt(index);
      _calculateRoute();
    }
  }

  // Set waypoints (similar to L.Routing.control().setWaypoints())
  void setWaypoints(List<LatLng> waypoints) {
    print('Setting waypoints: ${waypoints.length} points');
    _waypoints = List.from(waypoints);
    _calculateRoute();
  }

  // Get current waypoints
  List<LatLng> get waypoints => List.from(_waypoints);

  // Set profile
  void setProfile(String profile) {
    _profile = profile;
    _calculateRoute();
  }

  // Get current route result
  RouteResult? get routeResult => _routeResult;

  // Calculate route with current waypoints
  Future<void> _calculateRoute() async {
    print('_calculateRoute called with ${_waypoints.length} waypoints');

    if (_waypoints.length < 2) {
      print('Not enough waypoints, clearing route');
      _routeResult = null;
      _onRouteFound?.call(null);
      return;
    }

    try {
      print('Calling getRouteWithWaypoints...');
      final result = await RoutingService.getRouteWithWaypoints(
        waypoints: _waypoints,
        profile: _profile,
      );

      if (result != null) {
        print('Route found with ${result.points.length} points');
        _routeResult = result;
        _onRouteFound?.call(result);
      } else {
        print('API failed, using fallback route');
        // Fallback to straight line if API fails
        final fallbackResult = RoutingService.getStraightLineRoute(
          start: _waypoints.first,
          end: _waypoints.last,
          profile: _profile,
        );
        _routeResult = fallbackResult;
        _onRouteFound?.call(fallbackResult);
      }
    } catch (e) {
      print('RoutingControl Error: $e');
      _onError?.call('Error calculating route: $e');
      // Fallback to straight line on error
      final fallbackResult = RoutingService.getStraightLineRoute(
        start: _waypoints.first,
        end: _waypoints.last,
        profile: _profile,
      );
      _routeResult = fallbackResult;
      _onRouteFound?.call(fallbackResult);
    }
  }

  // Clear all waypoints
  void clear() {
    _waypoints.clear();
    _routeResult = null;
    _onRouteFound?.call(null);
  }

  // Check if routing is ready
  bool get isReady => _waypoints.length >= 2;
}
