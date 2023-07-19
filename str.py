import os

def get_project_structure(root_dir):
    structure = ""
    indentation = 0

    for root, dirs, files in os.walk(root_dir):
        if root != root_dir:
            structure += f"{' ' * indentation}|- {os.path.basename(root)}\n"
            indentation += 2

        for file in files:
            structure += f"{' ' * indentation}|- {file}\n"

    return structure

# Replace 'path_to_your_project_directory' with the actual path of your project directory
project_directory = 'C:/xampp/htdocs/mailer'
project_structure_string = get_project_structure(project_directory)
print(project_structure_string)