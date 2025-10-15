import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/faskes.dart';

class ApiService {
  static const String baseUrl = 'http://10.84.89.143:8001/api';

  static Future<Map<String, dynamic>> getFaskes({
    String? search,
    String? type,
    String? status,
    int page = 1,
  }) async {
    try {
      final Map<String, String> queryParams = {'page': page.toString()};

      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }

      if (type != null && type.isNotEmpty) {
        queryParams['type'] = type;
      }

      if (status != null && status.isNotEmpty) {
        queryParams['status'] = status;
      }

      final uri = Uri.parse(
        '$baseUrl/faskes',
      ).replace(queryParameters: queryParams);

      final response = await http.get(
        uri,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return {
          'success': true,
          'data': data['data'],
          'message': data['message'],
        };
      } else {
        return {
          'success': false,
          'message': 'Gagal mengambil data faskes',
          'data': null,
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: $e', 'data': null};
    }
  }

  static Future<List<Faskes>> getAllFaskes() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/faskes'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] && data['data'] != null) {
          final List<dynamic> faskesList = data['data']['data'] ?? data['data'];
          return faskesList.map((json) => Faskes.fromJson(json)).toList();
        }
      }
      return [];
    } catch (e) {
      print('Error fetching all faskes: $e');
      return [];
    }
  }
}
