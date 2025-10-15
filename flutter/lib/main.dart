import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'providers/faskes_provider.dart';
import 'screens/home_screen.dart';
import 'widgets/location_permission_widget.dart';

void main() {
  runApp(const FaskesApp());
}

class FaskesApp extends StatelessWidget {
  const FaskesApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [ChangeNotifierProvider(create: (_) => FaskesProvider())],
      child: MaterialApp(
        title: 'FASKES - Fasilitas Kesehatan',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          colorScheme: ColorScheme.fromSeed(
            seedColor: Colors.cyan,
            brightness: Brightness.light,
          ),
          useMaterial3: true,
          appBarTheme: const AppBarTheme(centerTitle: true, elevation: 0),
          elevatedButtonTheme: ElevatedButtonThemeData(
            style: ElevatedButton.styleFrom(
              elevation: 2,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
            ),
          ),
        ),
        home: const LocationPermissionWidget(child: HomeScreen()),
      ),
    );
  }
}
